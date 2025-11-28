// frontend/src/lib/api-client.ts
import axios from "axios";
import type { AxiosInstance, AxiosRequestConfig } from "axios";

export interface CouponImage {
    id: number;
    url: string; // /storage/1/01KB51R6EC9H7ZBH65KAB90VRK.png
    thumbnail: string; // /storage/1/conversions/01KB51R6EC9H7ZBH65KAB90VRK-thumbnail.jpg
    name: string;
}

export interface Coupon {
    id: number;
    selling_price: {
        amount: string;
        currency: string;
        formatted: string;
    };
    denomination: {
        amount: string;
        currency: string;
        formatted: string;
    };
    coupon_code: string;
    validity_days: number;
    is_available: boolean;
    is_low_stock: boolean;
    operator: {
        id: number;
        name: string;
        code: string;
        logo_url: string | null;
        country: {
            id: number;
            name: string;
            code: string;
            currency: string;
        };
    };
    plan_type: {
        id: number;
        name: string;
        description: string;
    };
    images: CouponImage[]; // ✅ Added images array
    created_at: string;
    updated_at: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: {
        first: string;
        last: string;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        links: PaginationLink[];
        path: string;
        per_page: number;
        to: number;
        total: number;
    };
}

class ApiClient {
    private client: AxiosInstance;
    private isTauri: boolean;

    constructor() {
        this.isTauri =
            typeof window !== "undefined" &&
            (window as any).__TAURI__ !== undefined;

        this.client = axios.create({
            baseURL: this.getBaseUrl(),
            timeout: 10000,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        });

        this.setupInterceptors();
    }

    private getBaseUrl(): string {
        if (this.isTauri) {
            return "http://localhost:8000/api";
        }

        return "http://localhost:8000/api";
    }

    private setupInterceptors() {
        // Request interceptor
        this.client.interceptors.request.use(
            (config) => {
                // Add auth token if available
                const token = localStorage.getItem("auth_token");
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }

                if (this.isTauri) {
                    config.headers["X-Platform"] = "tauri";
                }

                return config;
            },
            (error) => Promise.reject(error),
        );

        // Response interceptor
        this.client.interceptors.response.use(
            (response) => response,
            async (error) => {
                if (error.response?.status === 401) {
                    // Handle token refresh or logout
                    localStorage.removeItem("auth_token");
                    window.location.href = "/login";
                }
                return Promise.reject(error);
            },
        );
    }

    // ✅ PUBLIC METHODS - Expose API methods
    async get<T = any>(url: string, config?: AxiosRequestConfig): Promise<T> {
        const response = await this.client.get<T>(url, config);
        return response.data;
    }

    async post<T = any>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig,
    ): Promise<T> {
        const response = await this.client.post<T>(url, data, config);
        return response.data;
    }

    async put<T = any>(
        url: string,
        data?: any,
        config?: AxiosRequestConfig,
    ): Promise<T> {
        const response = await this.client.put<T>(url, data, config);
        return response.data;
    }

    async delete<T = any>(
        url: string,
        config?: AxiosRequestConfig,
    ): Promise<T> {
        const response = await this.client.delete<T>(url, config);
        return response.data;
    }
}

// ✅ Image URL Helper Function
// frontend/src/lib/api-client.ts
// src/lib/api-client.ts
// ✅ Get first available image for a coupon

// src/lib/api-client.ts
export function getImageUrl(
  imagePath: string | null | undefined,
  type: "full" | "thumbnail" = "full",
): string {
  // ✅ Handle null/undefined
  if (!imagePath) {
    return type === "full"
      ? "/api/placeholder/400/300"
      : "/api/placeholder/200/150";
  }

  const trimmedPath = imagePath.trim();

  // ✅ SMART LOGIC: Check if URL is already complete
  if (trimmedPath.startsWith('http://') || trimmedPath.startsWith('https://')) {
    console.log(`✅ getImageUrl: Already full URL: ${trimmedPath}`);
    return trimmedPath; // ✅ Return as-is
  }

  // ✅ Clean relative path
  let cleanPath = trimmedPath;
  if (!cleanPath.startsWith('/')) {
    cleanPath = `/${cleanPath}`;
  }

  // ✅ Platform detection
  const isTauri = typeof window !== "undefined" &&
                  (window as any).__TAURI__ !== undefined &&
                  (window as any).__TAURI__.platform !== undefined;

  console.log(`🔍 getImageUrl - isTauri: ${isTauri}, Path: "${cleanPath}"`);

  if (isTauri) {
    return `http://localhost:8000${cleanPath}`;
  }

  // Web: Relative path
  return cleanPath;
}

// frontend/src/lib/api-client.ts

// frontend/src/lib/api-client.ts

// src/lib/api-client.ts

// src/lib/api-client.ts
export function getCouponImage(coupon: Coupon | null | undefined): string {
  if (!coupon?.images?.[0]?.url) {
    console.log('⚠️ getCouponImage: No images, using placeholder');
    return "/api/placeholder/400/300";
  }

  const rawPath = coupon.images[0].url.trim();

  // ✅ SMART LOGIC: Check if already full URL
  if (rawPath.startsWith('http://') || rawPath.startsWith('https://')) {
    console.log(`✅ getCouponImage(${coupon.id}): Already full URL: ${rawPath}`);
    return rawPath;
  }

  console.log(`🔍 getCouponImage(${coupon.id}): Raw path = "${rawPath}"`);

  // Use getImageUrl for consistent logic
  const finalUrl = getImageUrl(rawPath);
  console.log(`✅ getCouponImage(${coupon.id}): Final URL = "${finalUrl}"`);

  return finalUrl;
}

// src/lib/api-client.ts
// src/lib/api-client.ts
export async function fetchCouponById(id: number): Promise<Coupon | null> {
  try {
    console.log(`🔍 Fetching coupon ${id}...`);

    const response = await apiClient.get<{ data: Coupon }>(`/coupons/${id}`);
    const coupon = response.data; // ✅ Extract .data like fetchCoupons

    console.log('✅ Coupon fetched:', coupon.coupon_code);
    console.log('✅ Images count:', coupon.images?.length || 0);

    return coupon;

  } catch (error: any) {
    if (error.response?.status === 404) {
      console.warn(`Coupon ${id} not found`);
      return null;
    }
    console.error('fetchCouponById error:', error);
    return null;
  }
}

// ✅ Also add error handling utility
export async function apiRequest<T>(
    url: string,
    options: RequestInit = {},
): Promise<T | null> {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                "Content-Type": "application/json",
                ...options.headers,
            },
        });

        if (!response.ok) {
            throw new Error(`API Error: ${response.status}`);
        }

        return (await response.json()) as T;
    } catch (error) {
        console.error(`API request failed for ${url}:`, error);
        return null;
    }
}

const apiClient = new ApiClient();

// ✅ FIXED: Use public get method instead of accessing private client
export async function fetchCoupons(
    page: number = 1,
): Promise<PaginatedResponse<Coupon>> {
    return apiClient.get<PaginatedResponse<Coupon>>(`/coupons?page=${page}`);
}

// ✅ Export public API methods for convenience
export const api = {
    get: apiClient.get.bind(apiClient),
    post: apiClient.post.bind(apiClient),
    put: apiClient.put.bind(apiClient),
    delete: apiClient.delete.bind(apiClient),
    fetchCoupons,
};

export { apiClient };
