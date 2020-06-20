// Views
import Home from "./components/Home";
import Profile from "./components/Profile";
import Login from "./components/Login";
import RegisterCustomer from "./components/RegisterCustomer";
import AuthContainer from "./components/AuthContainer";
import Container from "./components/Container";

import Dashboard from "./components/Dashboard";
import Tables from "./components/Tables";
import Maps from "./components/Maps";
import BadGateway from "./components/BadGateway";
import AccountInfo from "./components/AccountInfo";
import Transaction from "./components/Transaction";
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
                path: "/topup",
                name: "Topup",
                props: { page: 4 },
                component: Topup
            },
            {
                path: "/transactions",
                name: "Transactions",
                props: { page: 5 },
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
                props: { page: 6 },
                component: RegisterCustomer
            },
            {
                path: "/404",
                name: "BadGateway",
                props: { page: 7 },
                component: BadGateway
            },
            {
                path: "*",
                props: { page: 8 },
                redirect: "/404"
            }
        ]
    }
];
