import { Context } from "node:vm";
import { fetchCustomers } from "../services/customer.service.ts";

export const getCustomers = async (c: Context) => {
    const customers = await fetchCustomers();
    return c.json(customers);
};