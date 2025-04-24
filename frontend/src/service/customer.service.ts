import { fetchNonMembers } from "../api/index.ts";
import { Customer } from "../domain/customer.domain.ts";
import { addNonMember as apiAddNonMember } from "../api/customers.ts";

export async function getNonMembers(): Promise<Customer[]> {
  const data = await fetchNonMembers();
  return data as Customer[];
}

export async function addNonMember(nonMember: {
  firstName: string;
  lastName: string;
  contactNumber: string;
}): Promise<void> {
  await apiAddNonMember(nonMember);
}