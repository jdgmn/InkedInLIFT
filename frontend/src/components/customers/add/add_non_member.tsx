import React, { useState } from "react";
import { addNonMember } from "../../../service/customer.service.ts";

export default function AddNonMember() {
  const [firstName, setFirstName] = useState("");
  const [lastName, setLastName] = useState("");
  const [contactNumber, setContactNumber] = useState("");

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await addNonMember({ firstName, lastName, contactNumber });
      alert("Non-member added successfully!");
      setFirstName("");
      setLastName("");
      setContactNumber("");
    } catch (error) {
      console.error(error);
      alert("Failed to add non-member.");
    }
  };

  return (
    <>
      <form onSubmit={handleSubmit}>
      <h1>Add Non-Member</h1>
      <div>
        <label>First Name:</label>
        <input
          type="text"
          value={firstName}
          onChange={(e) => setFirstName(e.target.value)}
          required
        />
      </div>
      <div>
        <label>Last Name:</label>
        <input
          type="text"
          value={lastName}
          onChange={(e) => setLastName(e.target.value)}
          required
        />
      </div>
      <div>
        <label>Contact Number:</label>
        <input
          type="text"
          value={contactNumber}
          onChange={(e) => setContactNumber(e.target.value)}
          required
        />
      </div>
      <button type="submit">Add Non-Member</button>
    </form>
    </>
  );
}