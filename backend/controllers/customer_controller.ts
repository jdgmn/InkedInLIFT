import { Context } from "node:vm";
import { fetchCustomers } from "../services/customer_service.ts";

export const getCustomers = async (c: Context) => {
    const customers = await fetchCustomers();
    return c.json(customers);
};