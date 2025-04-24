import React from 'react';
import { Route, Routes, Link } from 'react-router-dom';
import ViewNonMembers from "./components/customers/view/view_non_members.tsx";
import AddNonMember from "./components/customers/add/add_non_member.tsx";
function App() {
  return (
    <>
      <nav>
        <ul>
          <li>
            <Link to="/">Home</Link>
          </li>
          <li>
            <Link to="/customers">Customers</Link>
          </li>
          <li>
            <Link to="/members">Members</Link>
          </li>
          <li>
            <Link to="/non-members">Non-Members</Link> 
          </li>
          <li>
            <Link to="/add-non-member">Add Non-Member</Link>
          </li>
        </ul>
      </nav>

      <Routes>
        <Route path="/" element={<h1>Welcome to the Customer Management System</h1>} />
        <Route path="/customers" element={<h1>View Customers</h1>} />
        <Route path="/members" element={<h1>View Members</h1>} />
        <Route path="/non-members" element={<ViewNonMembers />} /> 
        <Route path="/add-non-member" element={<AddNonMember />} />
      </Routes>
    </>
  );
}

export default App;