import { jsx as _jsx, jsxs as _jsxs } from "react/jsx-runtime";
import { featuredProfiles } from '../assets/mockData';
export function ProfileSpotlight() {
    return (_jsxs("section", { className: "glass-card", children: [_jsxs("div", { style: { display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1.25rem' }, children: [_jsxs("div", { children: [_jsx("p", { style: { color: 'var(--accent)', fontWeight: 600, fontSize: '0.9rem' }, children: "Spotlight temps r\u00E9el" }), _jsx("h2", { style: { fontSize: '1.75rem', marginTop: '0.25rem' }, children: "Profils compatibles autour de toi" })] }), _jsx("p", { style: { color: 'var(--text-muted)', fontSize: '0.9rem' }, children: "Bas\u00E9 sur ton mood du jour" })] }), _jsx("div", { style: {
                    display: 'grid',
                    gridTemplateColumns: 'repeat(auto-fit, minmax(220px, 1fr))',
                    gap: 'var(--grid-gap)'
                }, children: featuredProfiles.map((profile) => (_jsxs("article", { className: "glass-card", style: {
                        padding: '0',
                        overflow: 'hidden',
                        borderRadius: '1.5rem',
                        display: 'flex',
                        flexDirection: 'column'
                    }, children: [_jsx("div", { style: {
                                aspectRatio: '4 / 5',
                                backgroundImage: `url(${profile.image})`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center'
                            } }), _jsxs("div", { style: { padding: '1.25rem' }, children: [_jsx("h3", { style: { fontSize: '1.25rem' }, children: profile.name }), _jsx("p", { style: { color: 'var(--text-muted)' }, children: profile.location }), _jsxs("div", { style: {
                                        marginTop: '1rem',
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center'
                                    }, children: [_jsx("span", { style: {
                                                padding: '0.4rem 0.9rem',
                                                borderRadius: '999px',
                                                background: 'rgba(255,255,255,0.08)',
                                                fontSize: '0.9rem'
                                            }, children: profile.vibe }), _jsxs("span", { style: { fontWeight: 700, fontSize: '1.3rem' }, children: [profile.score, "%"] })] })] })] }, profile.name))) })] }));
}
