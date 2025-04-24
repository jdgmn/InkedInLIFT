import React from "react";
import { useEffect, useState } from "react";
import { getNonMembers } from "../../../service/customer.service.ts";
import { Customer } from "../../../domain/customer.domain.ts";

export default function ViewNonMembers() {
  const [nonMembers, setNonMembers] = useState<Customer[]>([]);

  useEffect(() => {
    getNonMembers()
      .then((data) => setNonMembers(data))
      .catch((error) => console.error(error));
  }, []);

  return (
    <div>
      <h1>Non-Members</h1>
      <ul>
        {nonMembers.map((nonMember) => (
          <li key={nonMember.id}>
            {nonMember.fullName} {nonMember.contactNumber}
          </li>
        ))}
      </ul>
    </div>
  );
}