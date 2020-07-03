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
import TransferInterbank from "./components/TransferInterbank";
import Topup from "./components/Topup";
export const routes = [
    { path: "/", name: "Login", component: Login },
    {
        path: "",
        component: Container,
        children: [
            {
                path: "/dashboard",
                name: "Dashboard",
                component: Dashboard,
                props: { page: 1 },
                alias: "/dashboard"
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
                path: "/transactions",
                name: "Transactions",
                props: { page: 6 },
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
                props: { page: 7 },
                component: RegisterCustomer
            },
            {
                path: "/404",
                name: "BadGateway",
                props: { page: 8 },
                component: Tables
            },
            {
                path: "*",
                props: { page: 9 },
                redirect: "/404"
            }
        ]
    }
];
