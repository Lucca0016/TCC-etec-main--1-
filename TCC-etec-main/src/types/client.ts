export class ApiClient {
  private baseUrl = '/TCC-etec/api';

  async get<T>(endpoint: string): Promise<T> {
    const token = (window as any).__CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      credentials: 'same-origin',
      headers: token ? { 'X-CSRF-Token': token } : {},
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
  }

  async post<T>(endpoint: string, data: unknown): Promise<T> {
    const token = (window as any).__CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers: Record<string,string> = { 'Content-Type': 'application/json' };
    if (token) headers['X-CSRF-Token'] = token;
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      method: 'POST',
      credentials: 'same-origin',
      headers,
      body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
  }

  async put<T>(endpoint: string, data: unknown): Promise<T> {
    const token = (window as any).__CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers: Record<string,string> = { 'Content-Type': 'application/json' };
    if (token) headers['X-CSRF-Token'] = token;
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      method: 'PUT',
      credentials: 'same-origin',
      headers,
      body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
  }

  async delete<T>(endpoint: string): Promise<T> {
    const token = (window as any).__CSRF_TOKEN || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers: Record<string,string> = {};
    if (token) headers['X-CSRF-Token'] = token;
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      method: 'DELETE',
      credentials: 'same-origin',
      headers,
    });
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    return response.json();
  }
}

export const api = new ApiClient();
