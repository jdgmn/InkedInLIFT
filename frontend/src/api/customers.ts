const API_BASE_URL = "http://localhost:8000/api";

export async function fetchCustomers() {
  const response = await fetch(`${API_BASE_URL}/customers`);
  if (!response.ok) {
    throw new Error("Failed to fetch customers");
  }
  return response.json();
}