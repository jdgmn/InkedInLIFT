const API_BASE_URL = "http://localhost:8000/api";

export async function fetchNonMembers() {
  const response = await fetch(`${API_BASE_URL}/non-members`);
  if (!response.ok) {
    throw new Error("Failed to fetch non-members");
  }
  return response.json();
}

export async function addNonMember(nonMember: {
  firstName: string;
  lastName: string;
  contactNumber: string;
}) {
  const response = await fetch(`${API_BASE_URL}/non-members`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(nonMember),
  });

  if (!response.ok) {
    throw new Error("Failed to add non-member");
  }

  return response.json();
}