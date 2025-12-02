import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { questSteps } from '../assets/mockData';
export function QuestTimeline() {
    return (_jsxs("section", { className: "glass-card quest-timeline", children: [_jsxs("header", { className: "section-header", children: [_jsxs("div", { children: [_jsx("p", { children: "Flow gamifi\u00E9" }), _jsx("h2", { children: "La qu\u00EAte qui te fait matcher diff\u00E9remment" })] }), _jsx("span", { className: "pill ghost", children: "Mode story-driven" })] }), _jsx("div", { className: "timeline", children: questSteps.map((step, index) => (_jsxs("article", { className: "timeline-step", children: [_jsx("div", { className: "timeline-index", children: index + 1 }), _jsxs("div", { children: [_jsx("h3", { children: step.title }), _jsx("p", { children: step.description }), _jsx("small", { children: step.eta })] })] }, step.title))) })] }));
}
