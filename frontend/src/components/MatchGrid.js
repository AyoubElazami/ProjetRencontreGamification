import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
const matchBlocks = [
    {
        title: 'Rooms immersives',
        copy: 'Audio, vidéo et quêtes collaboratives pour briser la glace en 90 secondes.',
        gradient: 'linear-gradient(135deg, rgba(255,107,139,0.25), rgba(90,214,255,0.18))'
    },
    {
        title: 'IA Mood Link',
        copy: 'Algorithme propriétaire qui priorise l’alchimie émotionnelle en temps réel.',
        gradient: 'linear-gradient(135deg, rgba(90,214,255,0.2), rgba(255,255,255,0.05))'
    },
    {
        title: 'Quêtes gamifiées',
        copy: 'Mini défis co-créés par notre communauté pour découvrir les vraies affinités.',
        gradient: 'linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,107,139,0.18))'
    }
];
export function MatchGrid() {
    return (_jsxs("section", { className: "glass-card", children: [_jsxs("div", { style: { marginBottom: '1.25rem' }, children: [_jsx("p", { style: { color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }, children: "Exp\u00E9rience" }), _jsx("h2", { style: { fontSize: '1.75rem', marginTop: '0.25rem' }, children: "Tout est pens\u00E9 pour matcher diff\u00E9remment" })] }), _jsx("div", { style: {
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(240px, 1fr))',
                    gap: 'var(--grid-gap)'
                }, children: matchBlocks.map((block) => (_jsxs("article", { className: "glass-card", style: {
                        minHeight: '220px',
                        background: block.gradient,
                        display: 'flex',
                        flexDirection: 'column',
                        gap: '0.75rem'
                    }, children: [_jsx("h3", { style: { fontSize: '1.25rem' }, children: block.title }), _jsx("p", { style: { color: 'var(--text-muted)' }, children: block.copy }), _jsx("button", { style: {
                                marginTop: 'auto',
                                width: 'max-content',
                                padding: '0.4rem 0.9rem',
                                borderRadius: '999px',
                                border: '1px solid rgba(255,255,255,0.25)',
                                background: 'transparent',
                                color: '#fff',
                                fontWeight: 600
                            }, children: "Voir une d\u00E9mo" })] }, block.title))) })] }));
}
