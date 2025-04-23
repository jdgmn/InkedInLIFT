import React from "react";
import { useEffect, useState } from "react";
import { fetchCustomers } from "./api/index.ts";


interface Customer {
  id: number;
  fullName: string;
  contactNumber: string;
}

function App() {
  const [customers, setCustomers] = useState<Customer[]>([]);

  useEffect(() => {
    fetchCustomers()
      .then((data) => setCustomers(data))
      .catch((error) => console.error(error));
  }, []);

  return (
    <div>
      <h1>Customers</h1>
      <ul>
        {customers.map((customer) => (
          <li key={customer.id}>{customer.fullName} {customer.contactNumber}</li>
        ))}
      </ul>
    </div>
  );
}

export default App;