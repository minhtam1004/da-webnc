// Views
import Home from "./components/Home";
import Profile from "./components/Profile";
import Login from "./components/Login";
import RegisterCustomer from "./components/RegisterCustomer";
import AuthContainer from "./components/AuthContainer";
import Container from "./components/Container";

import Dashboard from "./components/Dashboard";
import Tables from "./components/Transaction/Receiving";
import Maps from "./components/Maps";
import BadGateway from "./components/BadGateway";
import AccountInfo from "./components/AccountInfo";
import Transaction from "./components/Transaction";
import ListEmployee from "./components/ListEmployee";
import ListCustomer from "./components/ListCustomer";
import TransferInterbank from "./components/TransferInterbank";
import Topup from "./components/Topup";
export const routes = [
    { path: "/login", name: "Login", component: Login },
    {
        path: "",
        component: Container,
        children: [
            {
                path: "/",
                name: "Dashboard",
                component: Dashboard,
                props: { page: 1 },
                alias: "/dashboard",
                meta: { role: 3 }
              
            },
            {
                path: "/profile",
                name: "Profile",
                props: { page: 2 },
                component: Profile
            },
            {
                path: "/account-info",
                name: "AccountInfo",
                props: { page: 3 },
                component: AccountInfo
            },
            {
                path: "/transfers",
                name: "Topup",
                props: { page: 4 },
                component: Topup
            },
            {
                path: "/transfers-interbank",
                name: "TransferInterbank",
                props: { page: 5 },
                component: TransferInterbank
            },
            {
                path: "/employees",
                name: "ListEmployee",
                props: { page: 6 },
                component: ListEmployee
                
            },
            {
                path: "/customers",
                name: "ListCustomer",
                props: { page: 7 },
                component: ListCustomer
                
            },
            {
                path: "/transactions",
                name: "Transactions",
                props: { page: 8 },
                component: Transaction
            },

            // {
            //     path: "/maps",
            //     name: "Maps",
            //     props: { page: 6 },
            //     component: Maps
            // },
            {
                path: "/register-customer",
                name: "RegisterCustomer",
                props: { page: 9 },
                component: RegisterCustomer
            },
            {
                path: "/404",
                name: "BadGateway",
                props: { page: 10 },
                component: Tables
            },
            {
                path: "*",
                props: { page: 11 },
                redirect: "/404"
            }
        ]
    }
];
