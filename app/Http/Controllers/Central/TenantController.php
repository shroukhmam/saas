<?php

namespace App\Http\Controllers\Central;

use App\Enums\TenantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Central\StoreTenantRequest;
use App\Models\Central\Plan;
use App\Models\Central\Tenant;
use App\Services\TenantProvisioningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TenantController extends Controller
{
    public function __construct(private readonly TenantProvisioningService $provisioning) {}

    public function index(Request $request): View
    {
        $status = TenantStatus::tryFrom((string) $request->query('status'));
        $search = trim((string) $request->query('q'));
        $planId = $request->integer('plan') ?: null;

        $tenants = Tenant::query()
            ->with(['plan', 'domains'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($planId, fn ($q) => $q->where('plan_id', $planId))
            ->when($search !== '', function ($q) use ($search) {
                $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $search).'%';
                $q->where(fn ($w) => $w->where('name', 'ilike', $like)
                    ->orWhere('id', 'ilike', $like)
                    ->orWhere('owner_email', 'ilike', $like)
                    ->orWhereHas('domains', fn ($d) => $d->where('domain', 'ilike', $like)));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Tenant::query()->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('central.tenants.index', [
            'tenants' => $tenants,
            'counts' => $counts,
            'total' => $counts->sum(),
            'status' => $status,
            'search' => $search,
            'planId' => $planId,
            'plans' => Plan::orderBy('sort_order')->get(['id', 'name']),
        ]);
    }

    public function create(): View
    {
        return view('central.tenants.create', [
            'plans' => Plan::active()->get(),
            'centralDomain' => config('app.central_domain'),
        ]);
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        try {
            $result = $this->provisioning->provision($request->validated());
        } catch (Throwable $e) {
            Log::error('Tenant provisioning failed', ['id' => $request->input('id'), 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'The store could not be created, and nothing was left behind. Details are in the log.');
        }

        return redirect()->route('central.tenants.index')
            ->with('status', "Store \"{$result['tenant']->name}\" is ready.")
            ->with('owner_credentials', [
                'email' => $result['tenant']->owner_email,
                'password' => $result['owner_password'],
                'domain' => $request->validated('domain'),
            ]);
    }

    public function suspend(Tenant $tenant): RedirectResponse
    {
        $this->provisioning->suspend($tenant);

        return back()->with('status', "\"{$tenant->name}\" is suspended. Its panel and API are blocked.");
    }

    public function restore(Tenant $tenant): RedirectResponse
    {
        $this->provisioning->restore($tenant);

        return back()->with('status', "\"{$tenant->name}\" is restored.");
    }

    public function destroy(Request $request, Tenant $tenant): RedirectResponse
    {
        // Deleting drops the schema and all store data: the admin must type the ID.
        if ($request->input('confirm_id') !== $tenant->id) {
            return back()->with('error', "Type the tenant ID \"{$tenant->id}\" to delete the store.");
        }

        $name = $tenant->name;
        $this->provisioning->delete($tenant);

        return redirect()->route('central.tenants.index')->with('status', "\"{$name}\" and its schema are deleted.");
    }
}
