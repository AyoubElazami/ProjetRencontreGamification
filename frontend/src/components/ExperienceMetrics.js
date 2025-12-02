import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { experiences } from '../assets/mockData';
export function ExperienceMetrics() {
    return (_jsxs("section", { className: "glass-card", style: { display: 'grid', gap: '1rem' }, children: [_jsxs("header", { children: [_jsx("p", { style: { color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }, children: "Impact communaut\u00E9" }), _jsx("h2", { style: { fontSize: '1.75rem', marginTop: '0.25rem' }, children: "Des chiffres qui parlent" })] }), _jsx("div", { style: {
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))',
                    gap: 'var(--grid-gap)'
                }, children: experiences.map((exp) => (_jsxs("article", { className: "glass-card", style: { textAlign: 'center' }, children: [_jsx("p", { style: { fontSize: '2rem', fontWeight: 700 }, children: exp.value }), _jsx("p", { style: { color: 'var(--text-muted)', marginBottom: '0.5rem' }, children: exp.label }), _jsx("span", { style: { color: 'var(--accent)', fontWeight: 600 }, children: exp.trend })] }, exp.label))) })] }));
}
