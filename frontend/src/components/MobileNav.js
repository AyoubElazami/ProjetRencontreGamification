import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { Bell, ShieldCheck, UserRound } from 'lucide-react';
import { motion } from 'framer-motion';
import { menuTabs } from '../assets/mockData';
export function MobileNav() {
    return (_jsxs(motion.nav, { className: "glass-card hud-nav", initial: { opacity: 0, y: -10 }, animate: { opacity: 1, y: 0 }, transition: { duration: 0.5 }, children: [_jsxs("div", { className: "hud-brand", children: [_jsxs("div", { children: [_jsx("p", { children: "Rencontre+" }), _jsx("strong", { children: "Edition 2025" })] }), _jsx("span", { className: "status-pill", children: "Communaut\u00E9 priv\u00E9e" })] }), _jsx("div", { className: "menu-tabs", children: menuTabs.map((tab) => (_jsxs("button", { className: `menu-tab ${tab.active ? 'active' : ''}`, children: [_jsx("span", { children: tab.label }), tab.status ? _jsx("small", { children: tab.status }) : null] }, tab.label))) }), _jsxs("div", { className: "hud-actions", children: [_jsxs("button", { className: "badge ghost", children: [_jsx(ShieldCheck, { size: 16 }), "Safe mode"] }), _jsx("button", { className: "icon-button", children: _jsx(Bell, { size: 16 }) }), _jsxs("div", { className: "avatar-badge", children: [_jsx(UserRound, { size: 16 }), _jsx("span", { children: "Toi" })] })] })] }));
}
