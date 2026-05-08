@extends('layouts.app')

@section('title', 'Historial de Cambios')

@section('content')
<div class="changelog-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">Release Notes</h1>
            <p class="page-sub">Historial completo de actualizaciones y mejoras del sistema.</p>
        </div>
        <div class="header-actions">
            <div class="version-badge-lg">
                <span class="dot-pulse"></span>
                v2.7.4 Stable
            </div>
        </div>
    </div>

    @if(empty($entries))
        <div class="empty-state">
            <i class="fa-solid fa-file-lines"></i>
            <p>No se ha podido cargar el historial de cambios.</p>
        </div>
    @else
        <div class="timeline">
            @foreach($entries as $date => $entry)
                <div class="timeline-entry">
                    <div class="timeline-sidebar">
                        <div class="entry-date">{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</div>
                        <div class="entry-dot"></div>
                    </div>
                    <div class="entry-content-card">
                        <div class="entry-header">
                            <h2 class="entry-title">{{ $entry['title'] }}</h2>
                            @if(isset($entry['signature']))
                                <div class="entry-signature">
                                    <i class="fa-solid fa-user-check"></i>
                                    {{ $entry['signature'] }}
                                </div>
                            @endif
                        </div>

                        <div class="entry-body">
                            @foreach($entry['categories'] as $category => $items)
                                @php 
                                    // Sanitize category name for CSS class (e.g., "Refined (UI/UX)" -> "refined")
                                    $catClass = 'category-' . strtolower(explode(' ', trim($category))[0]); 
                                @endphp
                                <div class="category-section {{ $catClass }}">
                                    <h3 class="category-title">
                                        {{ $category }}
                                    </h3>
                                    <ul class="change-list">
                                        @foreach($items as $item)
                                            <li>
                                                @if($item['tag'])
                                                    <span class="item-tag">{{ $item['tag'] }}</span>
                                                @endif
                                                @if($item['label'])
                                                    <span class="change-label">{{ $item['label'] }}</span>
                                                @endif
                                                <span class="change-desc">{{ $item['description'] }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.changelog-container { max-width: 900px; margin: 0 auto; padding-bottom: 60px; }
.version-badge-lg {
    background: var(--surface); border: 1px solid var(--border);
    padding: 6px 14px; border-radius: 20px; font-family: 'IBM Plex Mono', monospace;
    font-size: 13px; font-weight: 600; color: var(--accent); display: flex; align-items: center; gap: 8px;
}
.dot-pulse { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 0 rgba(16, 185, 129, 0.4); animation: pulse 2s infinite; }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); } 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); } }

/* Timeline */
.timeline { position: relative; padding-top: 20px; }
.timeline::before {
    content: ''; position: absolute; left: 140px; top: 0; bottom: 0; width: 2px;
    background: var(--border); opacity: 0.5;
}
.timeline-entry { display: flex; gap: 40px; margin-bottom: 40px; position: relative; }
.timeline-sidebar { width: 140px; text-align: right; padding-top: 15px; flex-shrink: 0; position: relative; }
.entry-date { font-family: 'IBM Plex Mono', monospace; font-size: 12px; font-weight: 600; color: var(--muted); text-transform: uppercase; }
.entry-dot {
    position: absolute; right: -25px; top: 18px; width: 12px; height: 12px;
    background: var(--bg); border: 2px solid var(--accent); border-radius: 50%; z-index: 2;
}

.entry-content-card {
    flex-grow: 1; background: var(--surface); border: 1px solid var(--border);
    border-radius: 12px; padding: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.entry-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; border-bottom: 1px solid var(--border); padding-bottom: 15px; }
.entry-title { font-size: 18px; font-weight: 700; color: var(--primary); letter-spacing: -0.02em; }
.entry-signature { font-family: 'IBM Plex Mono', monospace; font-size: 11px; color: var(--muted); background: var(--bg); padding: 4px 10px; border-radius: 6px; border: 1px solid var(--border); display: flex; align-items: center; gap: 6px; }

/* Categories */
.category-section { margin-bottom: 20px; }
.category-section:last-child { margin-bottom: 0; }
.category-title {
    font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
    padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 12px;
}

/* Category Colors & Bullets */
.category-added { --cat-color: #10b981; --cat-bg: rgba(16, 185, 129, 0.1); }
.category-fixed { --cat-color: #3b82f6; --cat-bg: rgba(59, 130, 246, 0.1); }
.category-changed { --cat-color: #f59e0b; --cat-bg: rgba(245, 158, 11, 0.1); }
.category-security { --cat-color: #ef4444; --cat-bg: rgba(239, 68, 68, 0.1); }
.category-removed { --cat-color: #6b7280; --cat-bg: rgba(107, 114, 128, 0.1); }
.category-refined, .category-improved { --cat-color: #8b5cf6; --cat-bg: rgba(139, 92, 246, 0.1); }
.category-general { --cat-color: var(--muted); --cat-bg: var(--bg); }

.category-title {
    font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em;
    padding: 2px 8px; border-radius: 4px; display: inline-block; margin-bottom: 12px;
    background: var(--cat-bg); color: var(--cat-color); border: 1px solid rgba(var(--cat-color), 0.2);
}

.change-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; }
.change-list li { font-size: 13.5px; color: var(--secondary); line-height: 1.5; position: relative; padding-left: 18px; }
.change-list li::before { 
    content: ''; position: absolute; left: 0; top: 7px; width: 6px; height: 6px; 
    border-radius: 50%; background: var(--cat-color); box-shadow: 0 0 5px var(--cat-bg);
}

.item-tag {
    font-family: 'IBM Plex Mono', monospace; font-size: 10px; font-weight: 700;
    text-transform: uppercase; padding: 1px 6px; border-radius: 4px;
    background: var(--bg); border: 1px solid var(--border); color: var(--accent);
    margin-right: 6px; vertical-align: middle;
}
.change-label { font-weight: 600; color: var(--primary); margin-right: 4px; }
.change-desc { color: var(--secondary); }

@media (max-width: 768px) {
    .timeline::before { left: 0; }
    .timeline-entry { flex-direction: column; gap: 10px; }
    .timeline-sidebar { width: 100%; text-align: left; padding-top: 0; padding-left: 20px; }
    .entry-dot { left: -6px; right: auto; top: 3px; }
    .entry-header { flex-direction: column; gap: 10px; }
}
</style>
@endsection
