import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { Gamepad2, HeartHandshake, Map, Rocket } from 'lucide-react';
const actions = [
    { icon: Rocket, label: 'Boost', detail: 'x2 visibilité', highlight: true },
    { icon: Gamepad2, label: 'Quêtes', detail: '5 nouvelles' },
    { icon: HeartHandshake, label: 'Matches', detail: '12 connectés' },
    { icon: Map, label: 'Events', detail: 'Paris, Lyon' }
];
export function ActionDock() {
    return (_jsx("section", { className: "floating-dock glass-card", children: actions.map(({ icon: Icon, label, detail, highlight }) => (_jsxs("button", { className: `dock-action ${highlight ? 'primary' : ''}`, children: [_jsx(Icon, { size: 18 }), _jsxs("div", { children: [_jsx("span", { children: label }), _jsx("small", { children: detail })] })] }, label))) }));
}
