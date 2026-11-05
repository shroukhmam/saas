<?php

namespace App\Http\Requests\Central;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /** Build the full domain from the subdomain, so it can be checked as unique. */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => strtolower(trim((string) $this->input('id'))),
            'subdomain' => strtolower(trim((string) $this->input('subdomain'))),
            'domain' => strtolower(trim((string) $this->input('subdomain'))).'.'.config('app.central_domain'),
            'trial' => $this->boolean('trial'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            // The schema name is "tenant_<id>": keep it a plain PostgreSQL identifier.
            'id' => ['required', 'string', 'regex:/^[a-z][a-z0-9_]{2,39}$/', Rule::unique('tenants', 'id')],
            'subdomain' => ['required', 'string', 'regex:/^[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?$/'],
            'domain' => ['required', Rule::unique('domains', 'domain'), Rule::notIn(config('tenancy.central_domains'))],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('is_active', true)],
            'owner_name' => ['required', 'string', 'max:100'],
            'owner_email' => ['required', 'email', 'max:255'],
            'trial' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'id.regex' => 'Use 3–40 lowercase letters, numbers or underscores, starting with a letter.',
            'id.unique' => 'A tenant with this ID already exists.',
            'subdomain.regex' => 'Use lowercase letters, numbers and dashes. Do not start or end with a dash.',
            'domain.unique' => 'Another store already uses this domain.',
        ];
    }
}
