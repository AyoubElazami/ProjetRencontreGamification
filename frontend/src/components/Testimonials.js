import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { testimonials } from '../assets/mockData';
export function Testimonials() {
    return (_jsxs("section", { className: "glass-card", children: [_jsxs("header", { style: { marginBottom: '1.25rem' }, children: [_jsx("p", { style: { color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }, children: "Retours b\u00EAta" }), _jsx("h2", { style: { fontSize: '1.75rem', marginTop: '0.25rem' }, children: "Ils ont test\u00E9, ils racontent" })] }), _jsx("div", { style: { display: 'flex', flexDirection: 'column', gap: '1.25rem' }, children: testimonials.map((item) => (_jsxs("blockquote", { className: "glass-card", style: { borderRadius: '1rem', fontSize: '1.1rem', lineHeight: 1.5 }, children: [_jsx("p", { style: { color: 'var(--text-muted)' }, children: item.quote }), _jsx("cite", { style: { display: 'block', marginTop: '0.75rem', fontWeight: 600 }, children: item.author })] }, item.author))) })] }));
}
