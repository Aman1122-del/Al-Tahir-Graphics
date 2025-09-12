@extends('layouts.app')

@section('content')
<style>
/* Progressive enhancement: smooth scene transitions and micro-interactions */
/* Accessibility helper */
.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}

/* Micro interactions for toolbar controls */
#editor-toolbar button,
#editor-toolbar label[for],
#editor-toolbar select{
    transition: transform 160ms cubic-bezier(.2,.8,.2,1), box-shadow 160ms cubic-bezier(.2,.8,.2,1), background-color 160ms ease, color 160ms ease, border-color 160ms ease;
    will-change: transform;
}
#editor-toolbar button:hover,
#editor-toolbar label[for]:hover,
#editor-toolbar select:hover{
    transform: translateY(-1px);
}
#editor-toolbar button:active,
#editor-toolbar label[for]:active,
#editor-toolbar select:active{
    transform: translateY(0) scale(.98);
}

/* Elements that participate in scene transitions */
[data-scene]{
    backface-visibility: hidden;
    transform: translateZ(0);
    will-change: opacity, transform;
    contain: layout paint;
}

@media (prefers-reduced-motion: reduce){
    #editor-toolbar button,
    #editor-toolbar label[for],
    #editor-toolbar select{ transition: none !important; transform: none !important; }
    [data-scene]{ transition: none !important; }
}
</style>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white dark:bg-gray-900 shadow rounded-lg p-4 sm:p-6">
        
       <!-- New Editor Layout: Left toolbar, Center canvas, Right properties -->
        <div class="flex gap-4">
            <!-- Left Sidebar Toolbar -->
            <aside class="w-64 shrink-0 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded p-3 h-full" role="complementary" aria-label="Editor tools">
                <nav id="editor-toolbar" class="flex flex-col gap-3" role="toolbar" aria-label="Editor toolbar">
                    <!-- Button color controls (global override) -->
                    <div class="flex items-center gap-2">
                        <label class="text-sm">Btn Text</label>
                        <input id="btn-text-color-picker" type="color" class="w-8 h-8 p-0 border rounded" value="#ffffff" title="Button text color">
                        <label class="text-sm">Btn BG</label>
                        <input id="btn-bg-color-picker" type="color" class="w-8 h-8 p-0 border rounded" value="#2563eb" title="Button background color">
                        <button id="btn-reset-colors" class="px-2 py-1 ml-1 text-xs rounded border" title="Reset button colors">Reset</button>
                    </div>

                    <!-- Primary Tools -->
                    <div class="flex flex-col gap-2" role="group" aria-label="Primary tools">
                        <div class="flex gap-1">
                            <button id="tool-select" class="px-3 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-pressed="true" aria-label="Select tool">🖱️ Select</button>
                            <button id="tool-move" class="px-3 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-pressed="false" aria-label="Move tool">✥ Move</button>
                            <button id="tool-brush" class="px-3 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-pressed="false" aria-label="Brush tool">🖌️ Brush</button>
                        </div>
                    </div>

                    <!-- Basic Tools -->
                    <div class="flex flex-col gap-2" role="group" aria-label="Add elements">
                        <button id="btn-add-text" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Add Text">📝 Add Text</button>
                        <div class="flex items-center gap-2">
                            <label for="image-upload" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded hover:bg-green-700 cursor-pointer focus:outline-none focus:ring-2 focus:ring-green-500" aria-label="Upload Image">🖼️ Upload Image</label>
                            <input id="image-upload" type="file" accept="image/*" class="hidden" />
                        </div>
                        <select id="shape-select" class="px-3 py-2 bg-purple-600 text-white text-sm font-medium rounded hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500" aria-label="Add Shape">
                            <option value="">Add Shape</option>
                            <option value="rectangle">Rectangle</option>
                            <option value="circle">Circle</option>
                            <option value="triangle">Triangle</option>
                            <option value="line">Line</option>
                            <option value="arrow">Arrow</option>
                        </select>
                    </div>

                    <!-- History Controls -->
                    <div class="flex gap-1" role="group" aria-label="History">
                        <button id="btn-undo" class="px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500" aria-label="Undo" disabled>↶</button>
                        <button id="btn-redo" class="px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500" aria-label="Redo" disabled>↷</button>
                    </div>

                    <!-- Group/Align Controls -->
                    <div class="flex flex-col gap-2" role="group" aria-label="Grouping and alignment">
                        <div class="flex gap-1">
                            <button id="btn-group" class="px-3 py-2 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500" aria-label="Group Objects">📦 Group</button>
                            <button id="btn-ungroup" class="px-3 py-2 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500" aria-label="Ungroup Objects">📤 Ungroup</button>
                        </div>
                        <select id="align-select" class="px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Align Objects">
                            <option value="">Align</option>
                            <option value="left">Left</option>
                            <option value="center">Center H</option>
                            <option value="right">Right</option>
                            <option value="top">Top</option>
                            <option value="middle">Middle V</option>
                            <option value="bottom">Bottom</option>
                        </select>
                    </div>

                    <!-- View Controls -->
                    <div class="flex gap-1" role="group" aria-label="View controls">
                        <button id="btn-zoom-out" class="px-2 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-label="Zoom Out">🔍-</button>
                        <span id="zoom-level" class="px-2 py-2 text-sm bg-slate-100 rounded">100%</span>
                        <button id="btn-zoom-in" class="px-2 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-label="Zoom In">🔍+</button>
                        <button id="btn-fit" class="px-2 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-label="Fit to Screen">⊞</button>
                    </div>

                    <!-- Grid & Panels -->
                    <div class="flex flex-col gap-2">
                        <button id="btn-grid" class="px-3 py-2 bg-teal-600 text-white text-sm rounded hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500" aria-label="Toggle Grid">🔲 Grid</button>
                        <button id="btn-layers" class="px-3 py-2 bg-cyan-600 text-white text-sm rounded hover:bg-cyan-700 focus:outline-none focus:ring-2 focus:ring-cyan-500" aria-label="Toggle Layers Panel">📋 Layers</button>
                    </div>

                    <!-- Color / Clipboard -->
                    <div class="flex items-center gap-2">
                        <input id="color-picker" type="color" class="w-10 h-10 p-0 border rounded cursor-pointer" title="Fill Color" aria-label="Change Fill Color" />
                        <button id="btn-copy" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500" aria-label="Copy Object">📋 Copy</button>
                        <button id="btn-delete" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500" aria-label="Delete Selected">🗑️ Delete</button>
                    </div>

                    <!-- Canvas Size Controls -->
                    <div class="flex flex-col gap-2" role="group" aria-label="Canvas size">
                        <label class="text-sm">Preset</label>
                        <select id="canvas-preset" class="px-3 py-2 bg-slate-600 text-white text-sm rounded hover:bg-slate-700" aria-label="Canvas size preset">
                            <option value="">Select preset</option>
                            <option value="A4" data-width="794" data-height="1123">A4 (794x1123)</option>
                            <option value="Letter" data-width="816" data-height="1056">Letter (816x1056)</option>
                            <option value="Instagram Post" data-width="1080" data-height="1080">Instagram Post (1080x1080)</option>
                            <option value="Instagram Story" data-width="1080" data-height="1920">Instagram Story (1080x1920)</option>
                            <option value="Facebook Cover" data-width="1640" data-height="859">Facebook Cover (1640x859)</option>
                            <option value="Twitter Header" data-width="1500" data-height="500">Twitter Header (1500x500)</option>
                        </select>
                        <div class="flex items-center gap-2">
                            <label class="text-sm" for="canvas-width-input">W</label>
                            <input id="canvas-width-input" type="number" min="100" step="1" class="w-24 px-2 py-1 text-sm rounded border" placeholder="Width" aria-label="Canvas width">
                            <label class="text-sm" for="canvas-height-input">H</label>
                            <input id="canvas-height-input" type="number" min="100" step="1" class="w-24 px-2 py-1 text-sm rounded border" placeholder="Height" aria-label="Canvas height">
                            <button id="btn-apply-canvas-size" class="px-3 py-1 text-sm rounded border hover:bg-gray-200" aria-label="Apply canvas size">Apply</button>
                        </div>
                    </div>

                    <!-- Export Controls -->
                    <div class="flex flex-col gap-2" role="group" aria-label="Save and export">
                        <div class="flex gap-2">
                            <button id="btn-save" class="px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700" aria-label="Save Project">💾 Save</button>
                            <label for="load-project" class="px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700 cursor-pointer" aria-label="Load Project">📂 Load</label>
                            <input id="load-project" type="file" accept=".json" class="hidden" />
                        </div>
                        <select id="export-select" class="px-3 py-2 bg-emerald-600 text-white text-sm rounded hover:bg-emerald-700" aria-label="Export Options">
                            <option value="">Export As</option>
                            <option value="png">PNG</option>
                            <option value="svg">SVG</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </nav>
            </aside>

            <!-- Center Canvas -->
            <main class="flex-1">
                <div class="relative w-full overflow-hidden rounded border border-gray-200 dark:border-gray-700">
                    <canvas id="design-canvas" class="block w-full"></canvas>
                    <canvas id="grid-canvas" class="absolute top-0 left-0 pointer-events-none hidden"></canvas>
                </div>
            </main>

            <!-- Right Properties Panel -->
            <aside class="w-80 shrink-0 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded p-3 max-h-[80vh] overflow-y-auto" role="complementary" aria-label="Properties panel">
                <!-- Enhanced Text Formatting Panel -->
                <div id="text-panel" class="hidden mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <select id="font-family" class="px-2 py-1 text-sm rounded border" aria-label="Font Family">
                            <option value="Arial">Arial</option>
                            <option value="Helvetica">Helvetica</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Courier New">Courier New</option>
                            <option value="Impact">Impact</option>
                        </select>
                        
                        <input id="font-size" type="number" min="8" max="200" value="24" class="px-2 py-1 text-sm rounded border" placeholder="Size" aria-label="Font Size">
                        
                        <input id="line-height" type="number" min="0.5" max="3" step="0.1" value="1.2" class="px-2 py-1 text-sm rounded border" placeholder="Line Height" aria-label="Line Height">
                        
                        <input id="letter-spacing" type="number" min="-10" max="50" step="0.1" value="0" class="px-2 py-1 text-sm rounded border" placeholder="Letter Spacing" aria-label="Letter Spacing">
                        
                        <div class="flex gap-1">
                            <button id="btn-bold" class="px-2 py-1 text-sm rounded border font-bold hover:bg-gray-200" aria-label="Bold">B</button>
                            <button id="btn-italic" class="px-2 py-1 text-sm rounded border italic hover:bg-gray-200" aria-label="Italic">I</button>
                            <button id="btn-underline" class="px-2 py-1 text-sm rounded border underline hover:bg-gray-200" aria-label="Underline">U</button>
                        </div>
                        
                        <select id="text-align" class="px-2 py-1 text-sm rounded border" aria-label="Text Alignment">
                            <option value="left">Left</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                            <option value="justify">Justify</option>
                        </select>
                        
                        <select id="text-case" class="px-2 py-1 text-sm rounded border" aria-label="Text Case">
                            <option value="">Case</option>
                            <option value="uppercase">UPPER</option>
                            <option value="lowercase">lower</option>
                            <option value="capitalize">Title</option>
                        </select>
                        
                        <input id="text-color" type="color" class="w-full h-8 rounded border" title="Text Color" aria-label="Text Color">
                    </div>
                </div>

                <!-- Enhanced Style Panel -->
                <div id="style-panel" class="hidden mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <input id="stroke-color" type="color" class="w-full h-8 rounded border" title="Stroke Color" aria-label="Stroke Color">
                        <input id="stroke-width" type="number" min="0" max="20" step="1" value="0" class="px-2 py-1 text-sm rounded border" placeholder="Stroke Width" aria-label="Stroke Width">
                        <input id="object-opacity" type="range" min="0" max="100" value="100" class="w-full" aria-label="Opacity">
                        <span class="text-sm self-center">Opacity: <span id="opacity-value">100</span>%</span>
                        
                        <div class="col-span-2">
                            <label class="text-sm block mb-1">Shadow</label>
                            <div class="flex gap-1">
                                <input id="shadow-x" type="number" min="-50" max="50" value="0" class="px-1 py-1 text-xs rounded border w-16" placeholder="X" aria-label="Shadow X">
                                <input id="shadow-y" type="number" min="-50" max="50" value="0" class="px-1 py-1 text-xs rounded border w-16" placeholder="Y" aria-label="Shadow Y">
                                <input id="shadow-blur" type="number" min="0" max="50" value="0" class="px-1 py-1 text-xs rounded border w-16" placeholder="Blur" aria-label="Shadow Blur">
                                <input id="shadow-color" type="color" class="w-8 h-8 rounded border" title="Shadow Color" aria-label="Shadow Color">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transform Panel -->
                <div id="transform-panel" class="hidden mb-4 p-3 bg-gray-50 dark:bg-gray-800 rounded">
                    <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
                        <div>
                            <label class="text-xs text-gray-600 block">X</label>
                            <input id="pos-x" type="number" step="0.1" class="w-full px-2 py-1 text-sm rounded border" aria-label="X Position">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 block">Y</label>
                            <input id="pos-y" type="number" step="0.1" class="w-full px-2 py-1 text-sm rounded border" aria-label="Y Position">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 block">W</label>
                            <input id="width" type="number" step="0.1" min="1" class="w-full px-2 py-1 text-sm rounded border" aria-label="Width">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 block">H</label>
                            <input id="height" type="number" step="0.1" min="1" class="w-full px-2 py-1 text-sm rounded border" aria-label="Height">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 block">Rotate</label>
                            <input id="rotation" type="number" step="1" class="w-full px-2 py-1 text-sm rounded border" aria-label="Rotation">
                        </div>
                        <div class="flex items-end">
                            <button id="btn-lock-aspect" class="px-2 py-1 text-xs rounded border hover:bg-gray-200" title="Lock Aspect Ratio" aria-label="Lock Aspect Ratio">🔒</button>
                        </div>
                    </div>
                </div>

                <!-- Layers Panel (moved to properties area) -->
                <div id="layers-panel" class="hidden bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded p-3">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Layers</h3>
                        <button id="btn-close-layers" class="text-gray-400 hover:text-gray-600" aria-label="Close Layers Panel">✕</button>
                    </div>
                    <div id="layers-list" class="space-y-1 max-h-96 overflow-y-auto">
                        <!-- Layers will be populated here -->
                    </div>
                </div>
            </aside>
        </div>

        <!-- Footer Status Bar -->
        <div id="editor-statusbar" class="mt-4 px-3 py-2 bg-gray-50 dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-700 text-sm flex items-center justify-between" role="status" aria-live="polite" aria-atomic="true">
            <div class="flex flex-wrap items-center gap-4">
                <span>Tool: <span id="status-tool">Select</span></span>
                <span>Canvas: <span id="status-canvas-size">—</span></span>
                <span>Zoom: <span id="status-zoom">—</span></span>
                <span>Selection: <span id="status-selection">None</span></span>
            </div>
            <div class="text-gray-500">Press ? for help</div>
        </div>

        <!-- Instructions -->
        <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
            <p><strong>Shortcuts:</strong> Ctrl+Z/Y (undo/redo), Ctrl+C/V (copy/paste), Ctrl+G (group), Delete, Arrow keys (nudge), Ctrl+mousewheel (zoom)</p>
        </div>
    </div>
</div>

<!-- External Libraries -->
<script src="https://cdn.jsdelivr.net/npm/fabric@5.3.0/dist/fabric.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<script>
(function() {
    'use strict';
    
    // Global variables
    let fabricCanvas;
    let history = [];
    let historyIndex = -1;
    let isRedoing = false;
    let gridEnabled = false;
    let snapToGrid = false;
    let gridSize = 20;
    let zoomLevel = 1;
    let aspectRatioLocked = false;
    let autosaveInterval;
    
    // Live region for announcements (a11y)
    const live = document.createElement('div');
    live.id = 'sr-live';
    live.className = 'sr-only';
    live.setAttribute('aria-live','polite');
    live.setAttribute('aria-atomic','true');
    document.addEventListener('DOMContentLoaded', () => { document.body.appendChild(live); });

    // Motion preference
    const prefersReducedMotion = () => window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Scene transition helpers
    function smoothShow(el) {
        if (!el) return;
        el.setAttribute('data-scene', '');
        el.setAttribute('aria-hidden', 'false');
        if (prefersReducedMotion()) {
            el.classList.remove('hidden');
            el.style.opacity = '';
            el.style.transform = '';
            return;
        }
        el.style.willChange = 'opacity, transform';
        el.style.opacity = '0';
        el.style.transform = 'scale(0.985)';
        el.classList.remove('hidden');
        requestAnimationFrame(() => {
            el.style.transition = 'opacity 180ms cubic-bezier(.2,.8,.2,1), transform 220ms cubic-bezier(.2,.8,.2,1)';
            el.style.opacity = '1';
            el.style.transform = 'scale(1)';
        });
        el.addEventListener('transitionend', () => {
            el.style.transition = '';
            el.style.willChange = '';
        }, { once: true });
    }

    function smoothHide(el, opts) {
        if (!el) return;
        const options = opts || {};
        el.setAttribute('data-scene', '');
        if (prefersReducedMotion()) {
            el.classList.add('hidden');
            el.setAttribute('aria-hidden', 'true');
            if (options.returnFocusTo && typeof options.returnFocusTo.focus === 'function') {
                options.returnFocusTo.focus();
            }
            return;
        }
        el.style.willChange = 'opacity, transform';
        el.style.transition = 'opacity 160ms cubic-bezier(.2,.8,.2,1), transform 180ms cubic-bezier(.2,.8,.2,1)';
        el.style.opacity = '0';
        el.style.transform = 'scale(0.992)';
        el.addEventListener('transitionend', () => {
            el.classList.add('hidden');
            el.setAttribute('aria-hidden', 'true');
            el.style.transition = '';
            el.style.willChange = '';
            if (options.returnFocusTo && typeof options.returnFocusTo.focus === 'function') {
                options.returnFocusTo.focus();
            }
        }, { once: true });
    }

    function announce(msg){ try { live.textContent = ''; live.textContent = msg; } catch(e){} }

    let lastFocusForLayers = null;

    // Initialize
    document.addEventListener('DOMContentLoaded', initializeEditor);
    
    function initializeEditor() {
        setupCanvas();
        setupEventListeners();
        setupKeyboardShortcuts();
        loadAutosave();
        startAutosave();
        updateUI();

        // Mark panels as scenes
        ['text-panel','style-panel','transform-panel','layers-panel','grid-canvas'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.setAttribute('data-scene','');
                // initialize aria-hidden state based on visibility
                const isHidden = el.classList.contains('hidden');
                el.setAttribute('aria-hidden', String(isHidden));
                // assign roles and labels for major panels
                if (id === 'text-panel') { el.setAttribute('role','region'); el.setAttribute('aria-label','Text formatting panel'); }
                if (id === 'style-panel') { el.setAttribute('role','region'); el.setAttribute('aria-label','Style panel'); }
                if (id === 'transform-panel') { el.setAttribute('role','region'); el.setAttribute('aria-label','Transform panel'); }
            }
        });

        // Aria linkage for layers toggle
        const layersBtn = document.getElementById('btn-layers');
        const layersPanel = document.getElementById('layers-panel');
        if (layersBtn && layersPanel) {
            layersBtn.setAttribute('aria-controls','layers-panel');
            layersBtn.setAttribute('aria-expanded', String(!layersPanel.classList.contains('hidden')));
            layersPanel.setAttribute('role','region');
            layersPanel.setAttribute('aria-label','Layers panel');
            layersPanel.setAttribute('aria-hidden', String(layersPanel.classList.contains('hidden')));
        }
    }
    
    function setupCanvas() {
        const canvasEl = document.getElementById('design-canvas');
        
        fabricCanvas = new fabric.Canvas('design-canvas', {
            preserveObjectStacking: true,
            backgroundColor: '#ffffff',
            selection: true,
            enableRetinaScaling: true
        });
        
        // Configure controls
        fabric.Object.prototype.transparentCorners = false;
        fabric.Object.prototype.cornerStyle = 'circle';
        fabric.Object.prototype.cornerColor = '#2563eb';
        fabric.Object.prototype.borderColor = '#2563eb';
        fabric.Object.prototype.cornerSize = 8;
        
        // Responsive canvas sizing
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);
        
        // Canvas event listeners
        fabricCanvas.on('object:added', () => {
            if (!isRedoing) saveState();
            updateLayers();
            updateUI();
        });
        
        fabricCanvas.on('object:removed', () => {
            if (!isRedoing) saveState();
            updateLayers();
            updateUI();
        });
        
        fabricCanvas.on('object:modified', () => {
            if (!isRedoing) saveState();
            updateTransformPanel();
        });
        
        fabricCanvas.on('selection:created', updatePanels);
        fabricCanvas.on('selection:updated', updatePanels);
        fabricCanvas.on('selection:cleared', () => {
            hidePanels();
            updateUI();
        });
        
        fabricCanvas.on('object:moving', handleSnapping);
        fabricCanvas.on('object:scaling', handleAspectRatio);
        
        // Mouse wheel zoom
        fabricCanvas.on('mouse:wheel', handleMouseWheel);
        
        // Initial state
        saveState();
    }
    
    function resizeCanvas() {
        const container = document.getElementById('design-canvas').parentElement;
        const width = container.clientWidth;
        const height = Math.max(400, Math.round(width * 0.6));
        
        fabricCanvas.setWidth(width);
        fabricCanvas.setHeight(height);
        fabricCanvas.requestRenderAll();
        
        setupGrid();
    }
    
    function setupGrid() {
        const gridCanvas = document.getElementById('grid-canvas');
        const ctx = gridCanvas.getContext('2d');
        
        gridCanvas.width = fabricCanvas.getWidth();
        gridCanvas.height = fabricCanvas.getHeight();
        
        if (!gridEnabled) return;
        
        ctx.clearRect(0, 0, gridCanvas.width, gridCanvas.height);
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 1;
        
        // Draw grid
        for (let x = 0; x <= gridCanvas.width; x += gridSize) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, gridCanvas.height);
            ctx.stroke();
        }
        
        for (let y = 0; y <= gridCanvas.height; y += gridSize) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(gridCanvas.width, y);
            ctx.stroke();
        }
    }
    
    function setupEventListeners() {
        // Basic tools
        document.getElementById('btn-add-text').addEventListener('click', addText);
        document.getElementById('image-upload').addEventListener('change', uploadImage);
        document.getElementById('shape-select').addEventListener('change', addShape);
        document.getElementById('color-picker').addEventListener('input', updateColor);
        document.getElementById('btn-copy').addEventListener('click', copyObject);
        document.getElementById('btn-delete').addEventListener('click', deleteObject);
        
        // History
        document.getElementById('btn-undo').addEventListener('click', undo);
        document.getElementById('btn-redo').addEventListener('click', redo);
        
        // Group/Align
        document.getElementById('btn-group').addEventListener('click', groupObjects);
        document.getElementById('btn-ungroup').addEventListener('click', ungroupObjects);
        document.getElementById('align-select').addEventListener('change', alignObjects);
        
        // View controls
        document.getElementById('btn-zoom-in').addEventListener('click', zoomIn);
        document.getElementById('btn-zoom-out').addEventListener('click', zoomOut);
        document.getElementById('btn-fit').addEventListener('click', fitToScreen);
        document.getElementById('btn-grid').addEventListener('click', toggleGrid);
        
        // Panel toggles
        document.getElementById('btn-layers').addEventListener('click', toggleLayersPanel);
        document.getElementById('btn-close-layers').addEventListener('click', () => {
            hideLayersPanel();
        });
        
        // Export/Save
        document.getElementById('btn-save').addEventListener('click', saveProject);
        document.getElementById('load-project').addEventListener('change', loadProject);
        document.getElementById('export-select').addEventListener('change', exportAs);
        
        // Text formatting
        setupTextFormatting();
        
        // Style panel
        setupStylePanel();
        
        // Transform panel
        setupTransformPanel();
    }
    
    function setupTextFormatting() {
        const controls = ['font-family', 'font-size', 'line-height', 'letter-spacing', 'text-align', 'text-case', 'text-color'];
        
        controls.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', updateTextProperties);
                if (el.type === 'number') {
                    el.addEventListener('input', updateTextProperties);
                }
            }
        });
        
        document.getElementById('btn-bold').addEventListener('click', toggleBold);
        document.getElementById('btn-italic').addEventListener('click', toggleItalic);
        document.getElementById('btn-underline').addEventListener('click', toggleUnderline);
    }
    
    function setupStylePanel() {
        document.getElementById('stroke-color').addEventListener('input', updateStrokeColor);
        document.getElementById('stroke-width').addEventListener('input', updateStrokeWidth);
        document.getElementById('object-opacity').addEventListener('input', updateOpacity);
        
        ['shadow-x', 'shadow-y', 'shadow-blur', 'shadow-color'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateShadow);
        });
    }
    
    function setupTransformPanel() {
        ['pos-x', 'pos-y', 'width', 'height', 'rotation'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('change', updateTransform);
                el.addEventListener('input', updateTransform);
            }
        });
        
        document.getElementById('btn-lock-aspect').addEventListener('click', toggleAspectRatio);
    }
    
    function setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            const isTextEditing = fabricCanvas.getActiveObject() && 
                                 fabricCanvas.getActiveObject().isEditing;
            
            if (isTextEditing) return;
            
            // Prevent default for our shortcuts
            const shortcuts = ['z', 'y', 'c', 'v', 'x', 'g', 'a', 'Delete', 'Backspace', 'ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'];
            
            if ((e.ctrlKey || e.metaKey) && shortcuts.includes(e.key)) {
                e.preventDefault();
            }
            
            // History
            if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                undo();
            } else if (((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'Z') || 
                      ((e.ctrlKey || e.metaKey) && e.key === 'y')) {
                redo();
            }
            
            // Copy/Paste/Cut
            else if ((e.ctrlKey || e.metaKey) && e.key === 'c') {
                copyObject();
            } else if ((e.ctrlKey || e.metaKey) && e.key === 'v') {
                pasteObject();
            } else if ((e.ctrlKey || e.metaKey) && e.key === 'x') {
                cutObject();
            }
            
            // Group/Ungroup
            else if ((e.ctrlKey || e.metaKey) && e.key === 'g') {
                if (e.shiftKey) {
                    ungroupObjects();
                } else {
                    groupObjects();
                }
            }
            
            // Select All
            else if ((e.ctrlKey || e.metaKey) && e.key === 'a') {
                selectAll();
            }
            
            // Delete
            else if (e.key === 'Delete' || e.key === 'Backspace') {
                deleteObject();
            }
            
            // Nudge
            else if (['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight'].includes(e.key)) {
                e.preventDefault();
                nudgeObject(e.key, e.shiftKey);
            }
        });
    }
    
    // Core Functions
    function saveState() {
        if (isRedoing) return;
        
        const state = JSON.stringify(fabricCanvas.toJSON(['selectable', 'hasControls']));
        
        // Remove future states if we're in the middle of history
        if (historyIndex < history.length - 1) {
            history = history.slice(0, historyIndex + 1);
        }
        
        history.push(state);
        historyIndex++;
        
        // Limit history size
        if (history.length > 50) {
            history = history.slice(-50);
            historyIndex = 49;
        }
        
        updateUI();
    }
    
    function undo() {
        if (historyIndex > 0) {
            isRedoing = true;
            historyIndex--;
            loadState(history[historyIndex]);
            isRedoing = false;
            updateUI();
        }
    }
    
    function redo() {
        if (historyIndex < history.length - 1) {
            isRedoing = true;
            historyIndex++;
            loadState(history[historyIndex]);
            isRedoing = false;
            updateUI();
        }
    }
    
    function loadState(state) {
        fabricCanvas.loadFromJSON(state, () => {
            fabricCanvas.requestRenderAll();
            updateLayers();
            updatePanels();
        });
    }
    
    function addText() {
        const text = new fabric.IText('Double-click to edit', {
            left: fabricCanvas.getWidth() / 2,
            top: fabricCanvas.getHeight() / 2,
            originX: 'center',
            originY: 'center',
            fontSize: 32,
            fill: '#111827',
            fontFamily: 'Arial',
            id: 'text_' + Date.now()
        });
        
        fabricCanvas.add(text).setActiveObject(text);
        fabricCanvas.requestRenderAll();
    }
    
    function uploadImage(e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(f) {
            fabric.Image.fromURL(f.target.result, function(img) {
                const maxW = fabricCanvas.getWidth() * 0.8;
                const maxH = fabricCanvas.getHeight() * 0.8;
                const scale = Math.min(maxW / img.width, maxH / img.height, 1);
                
                img.set({ 
                    left: fabricCanvas.getWidth()/2, 
                    top: fabricCanvas.getHeight()/2, 
                    originX: 'center', 
                    originY: 'center',
                    id: 'image_' + Date.now()
                });
                img.scale(scale);
                fabricCanvas.add(img).setActiveObject(img);
                fabricCanvas.requestRenderAll();
            });
        };
        reader.readAsDataURL(file);
        e.target.value = '';
    }
    
    function addShape(e) {
        const shape = e.target.value;
        let obj = null;
        
        const commonProps = {
            left: 100, 
            top: 100, 
            fill: '#e5e7eb', 
            stroke: '#374151', 
            strokeWidth: 2,
            id: shape + '_' + Date.now()
        };
        
        switch(shape) {
            case 'rectangle':
                obj = new fabric.Rect({ ...commonProps, width: 120, height: 80 });
                break;
            case 'circle':
                obj = new fabric.Circle({ ...commonProps, radius: 50 });
                break;
            case 'triangle':
                obj = new fabric.Triangle({ ...commonProps, width: 100, height: 100 });
                break;
            case 'line':
                obj = new fabric.Line([0, 0, 100, 0], { ...commonProps, fill: '', stroke: '#374151', strokeWidth: 3 });
                break;
            case 'arrow':
                obj = createArrow();
                break;
        }
        
        if (obj) {
            fabricCanvas.add(obj).setActiveObject(obj);
            fabricCanvas.requestRenderAll();
        }
        e.target.value = '';
    }
    
    function createArrow() {
        const line = new fabric.Line([0, 0, 80, 0], {
            stroke: '#374151',
            strokeWidth: 3,
            originX: 'center',
            originY: 'center'
        });
        
        const triangle = new fabric.Triangle({
            width: 15,
            height: 20,
            fill: '#374151',
            left: 80,
            originX: 'center',
            originY: 'center',
            angle: 90
        });
        
        return new fabric.Group([line, triangle], {
            left: 100,
            top: 100,
            id: 'arrow_' + Date.now()
        });
    }
    
    // ... (continuing with remaining functions in next part due to length)
    
    function updateColor(e) {
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        if (obj.type === 'i-text' || obj.type === 'text') {
            obj.set('fill', e.target.value);
        } else {
            obj.set('fill', e.target.value);
        }
        fabricCanvas.requestRenderAll();
    }
    
    function copyObject() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        obj.clone((cloned) => {
            window.clipboard = cloned;
        });
    }
    
    function pasteObject() {
        if (!window.clipboard) return;
        
        window.clipboard.clone((cloned) => {
            cloned.set({
                left: cloned.left + 20,
                top: cloned.top + 20,
                id: (cloned.type || 'object') + '_' + Date.now()
            });
            fabricCanvas.add(cloned);
            fabricCanvas.setActiveObject(cloned);
            fabricCanvas.requestRenderAll();
        });
    }
    
    function cutObject() {
        copyObject();
        deleteObject();
    }
    
    function deleteObject() {
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            if (obj.type === 'activeSelection') {
                obj.forEachObject((o) => fabricCanvas.remove(o));
            } else {
                fabricCanvas.remove(obj);
            }
            fabricCanvas.discardActiveObject();
            fabricCanvas.requestRenderAll();
        }
    }
    
    function groupObjects() {
        const selection = fabricCanvas.getActiveObject();
        if (!selection || selection.type !== 'activeSelection') return;
        
        const group = selection.toGroup();
        group.set('id', 'group_' + Date.now());
        fabricCanvas.requestRenderAll();
    }
    
    function ungroupObjects() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj || obj.type !== 'group') return;
        
        obj.toActiveSelection();
        fabricCanvas.requestRenderAll();
    }
    
    function alignObjects(e) {
        const alignment = e.target.value;
        if (!alignment) return;
        
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        const canvasWidth = fabricCanvas.getWidth();
        const canvasHeight = fabricCanvas.getHeight();
        
        switch(alignment) {
            case 'left':
                obj.set('left', obj.width / 2);
                break;
            case 'center':
                obj.set('left', canvasWidth / 2);
                break;
            case 'right':
                obj.set('left', canvasWidth - obj.width / 2);
                break;
            case 'top':
                obj.set('top', obj.height / 2);
                break;
            case 'middle':
                obj.set('top', canvasHeight / 2);
                break;
            case 'bottom':
                obj.set('top', canvasHeight - obj.height / 2);
                break;
        }
        
        obj.setCoords();
        fabricCanvas.requestRenderAll();
        e.target.value = '';
    }
    
    function selectAll() {
        const objects = fabricCanvas.getObjects();
        if (objects.length > 1) {
            const selection = new fabric.ActiveSelection(objects, { canvas: fabricCanvas });
            fabricCanvas.setActiveObject(selection);
            fabricCanvas.requestRenderAll();
        }
    }
    
    function nudgeObject(key, shift) {
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        const step = shift ? 10 : 1;
        
        switch(key) {
            case 'ArrowUp':
                obj.set('top', obj.top - step);
                break;
            case 'ArrowDown':
                obj.set('top', obj.top + step);
                break;
            case 'ArrowLeft':
                obj.set('left', obj.left - step);
                break;
            case 'ArrowRight':
                obj.set('left', obj.left + step);
                break;
        }
        
        obj.setCoords();
        fabricCanvas.requestRenderAll();
        updateTransformPanel();
    }
    
    // View Controls
    function zoomIn() {
        zoomLevel = Math.min(zoomLevel * 1.2, 5);
        fabricCanvas.setZoom(zoomLevel);
        updateZoomDisplay();
    }
    
    function zoomOut() {
        zoomLevel = Math.max(zoomLevel / 1.2, 0.1);
        fabricCanvas.setZoom(zoomLevel);
        updateZoomDisplay();
    }
    
    function fitToScreen() {
        zoomLevel = 1;
        fabricCanvas.setZoom(1);
        fabricCanvas.viewportTransform[4] = 0;
        fabricCanvas.viewportTransform[5] = 0;
        fabricCanvas.requestRenderAll();
        updateZoomDisplay();
    }
    
    function updateZoomDisplay() {
        document.getElementById('zoom-level').textContent = Math.round(zoomLevel * 100) + '%';
    }
    
    function handleMouseWheel(opt) {
        const delta = opt.e.deltaY;
        let zoom = fabricCanvas.getZoom();
        zoom *= 0.999 ** delta;
        
        if (zoom > 5) zoom = 5;
        if (zoom < 0.1) zoom = 0.1;
        
        zoomLevel = zoom;
        fabricCanvas.zoomToPoint({ x: opt.e.offsetX, y: opt.e.offsetY }, zoom);
        updateZoomDisplay();
        
        opt.e.preventDefault();
        opt.e.stopPropagation();
    }
    
    function toggleGrid() {
        gridEnabled = !gridEnabled;
        const gridCanvas = document.getElementById('grid-canvas');
        
        if (gridEnabled) {
            smoothShow(gridCanvas);
            document.getElementById('btn-grid').classList.add('bg-teal-800');
        } else {
            smoothHide(gridCanvas);
            document.getElementById('btn-grid').classList.remove('bg-teal-800');
        }
        
        setupGrid();
    }
    
    function handleSnapping(e) {
        if (!gridEnabled) return;
        
        const obj = e.target;
        const left = Math.round(obj.left / gridSize) * gridSize;
        const top = Math.round(obj.top / gridSize) * gridSize;
        
        obj.set({ left, top });
    }
    
    // Panel Management
    function updatePanels() {
        const obj = fabricCanvas.getActiveObject();
        
        hidePanels();
        
        if (obj) {
            updateTransformPanel();
            
            if (obj.type === 'i-text' || obj.type === 'text') {
                showTextPanel();
            } else {
                showStylePanel();
            }
        }
    }
    
    function hidePanels() {
        smoothHide(document.getElementById('text-panel'));
        smoothHide(document.getElementById('style-panel'));
        smoothHide(document.getElementById('transform-panel'));
    }
    
    function showTextPanel() {
        const panel = document.getElementById('text-panel');
        smoothShow(panel);
        
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            document.getElementById('font-family').value = obj.fontFamily || 'Arial';
            document.getElementById('font-size').value = obj.fontSize || 24;
            document.getElementById('line-height').value = obj.lineHeight || 1.2;
            document.getElementById('letter-spacing').value = (obj.charSpacing || 0) / 100;
            document.getElementById('text-align').value = obj.textAlign || 'left';
            document.getElementById('text-color').value = obj.fill || '#000000';
            
            // Update button states
            updateTextButtonStates();
        }
    }
    
    function showStylePanel() {
        smoothShow(document.getElementById('style-panel'));
        
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            document.getElementById('stroke-color').value = obj.stroke || '#000000';
            document.getElementById('stroke-width').value = obj.strokeWidth || 0;
            document.getElementById('object-opacity').value = (obj.opacity || 1) * 100;
            document.getElementById('opacity-value').textContent = Math.round((obj.opacity || 1) * 100);
            
            // Shadow properties
            const shadow = obj.shadow || {};
            document.getElementById('shadow-x').value = shadow.offsetX || 0;
            document.getElementById('shadow-y').value = shadow.offsetY || 0;
            document.getElementById('shadow-blur').value = shadow.blur || 0;
            document.getElementById('shadow-color').value = shadow.color || '#000000';
        }
    }
    
    function updateTransformPanel() {
        smoothShow(document.getElementById('transform-panel'));
        
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            document.getElementById('pos-x').value = Math.round(obj.left * 10) / 10;
            document.getElementById('pos-y').value = Math.round(obj.top * 10) / 10;
            document.getElementById('width').value = Math.round(obj.getScaledWidth() * 10) / 10;
            document.getElementById('height').value = Math.round(obj.getScaledHeight() * 10) / 10;
            document.getElementById('rotation').value = Math.round(obj.angle || 0);
        }
    }
    
    // Text Formatting
    function updateTextProperties(e) {
        const obj = fabricCanvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'text')) return;
        
        const prop = e.target.id;
        let value = e.target.value;
        
        switch(prop) {
            case 'font-family':
                obj.set('fontFamily', value);
                break;
            case 'font-size':
                obj.set('fontSize', parseInt(value));
                break;
            case 'line-height':
                obj.set('lineHeight', parseFloat(value));
                break;
            case 'letter-spacing':
                obj.set('charSpacing', parseFloat(value) * 100);
                break;
            case 'text-align':
                obj.set('textAlign', value);
                break;
            case 'text-case':
                if (value === 'uppercase') obj.set('text', obj.text.toUpperCase());
                else if (value === 'lowercase') obj.set('text', obj.text.toLowerCase());
                else if (value === 'capitalize') obj.set('text', obj.text.replace(/\w\S*/g, (txt) => 
                    txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()));
                break;
            case 'text-color':
                obj.set('fill', value);
                break;
        }
        
        fabricCanvas.requestRenderAll();
    }
    
    function toggleBold() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'text')) return;
        
        const isBold = obj.fontWeight === 'bold';
        obj.set('fontWeight', isBold ? 'normal' : 'bold');
        fabricCanvas.requestRenderAll();
        updateTextButtonStates();
    }
    
    function toggleItalic() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'text')) return;
        
        const isItalic = obj.fontStyle === 'italic';
        obj.set('fontStyle', isItalic ? 'normal' : 'italic');
        fabricCanvas.requestRenderAll();
        updateTextButtonStates();
    }
    
    function toggleUnderline() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj || (obj.type !== 'i-text' && obj.type !== 'text')) return;
        
        const isUnderlined = obj.underline;
        obj.set('underline', !isUnderlined);
        fabricCanvas.requestRenderAll();
        updateTextButtonStates();
    }
    
    function updateTextButtonStates() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        const boldBtn = document.getElementById('btn-bold');
        const italicBtn = document.getElementById('btn-italic');
        const underlineBtn = document.getElementById('btn-underline');
        
        boldBtn.style.backgroundColor = obj.fontWeight === 'bold' ? '#e5e7eb' : '';
        italicBtn.style.backgroundColor = obj.fontStyle === 'italic' ? '#e5e7eb' : '';
        underlineBtn.style.backgroundColor = obj.underline ? '#e5e7eb' : '';
    }
    
    // Style Panel Functions
    function updateStrokeColor(e) {
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            obj.set('stroke', e.target.value);
            fabricCanvas.requestRenderAll();
        }
    }
    
    function updateStrokeWidth(e) {
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            obj.set('strokeWidth', parseInt(e.target.value));
            fabricCanvas.requestRenderAll();
        }
    }
    
    function updateOpacity(e) {
        const obj = fabricCanvas.getActiveObject();
        if (obj) {
            const opacity = parseInt(e.target.value) / 100;
            obj.set('opacity', opacity);
            document.getElementById('opacity-value').textContent = e.target.value;
            fabricCanvas.requestRenderAll();
        }
    }
    
    function updateShadow() {
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        const x = parseInt(document.getElementById('shadow-x').value) || 0;
        const y = parseInt(document.getElementById('shadow-y').value) || 0;
        const blur = parseInt(document.getElementById('shadow-blur').value) || 0;
        const color = document.getElementById('shadow-color').value;
        
        if (x === 0 && y === 0 && blur === 0) {
            obj.set('shadow', null);
        } else {
            obj.set('shadow', new fabric.Shadow({
                color: color,
                blur: blur,
                offsetX: x,
                offsetY: y
            }));
        }
        
        fabricCanvas.requestRenderAll();
    }
    
    // Transform Functions
    function updateTransform(e) {
        const obj = fabricCanvas.getActiveObject();
        if (!obj) return;
        
        const prop = e.target.id;
        const value = parseFloat(e.target.value);
        
        switch(prop) {
            case 'pos-x':
                obj.set('left', value);
                break;
            case 'pos-y':
                obj.set('top', value);
                break;
            case 'width':
                if (aspectRatioLocked) {
                    const ratio = obj.getScaledHeight() / obj.getScaledWidth();
                    obj.scaleToWidth(value);
                    obj.scaleToHeight(value * ratio);
                } else {
                    obj.scaleToWidth(value);
                }
                break;
            case 'height':
                if (aspectRatioLocked) {
                    const ratio = obj.getScaledWidth() / obj.getScaledHeight();
                    obj.scaleToHeight(value);
                    obj.scaleToWidth(value * ratio);
                } else {
                    obj.scaleToHeight(value);
                }
                break;
            case 'rotation':
                obj.set('angle', value);
                break;
        }
        
        obj.setCoords();
        fabricCanvas.requestRenderAll();
    }
    
    function toggleAspectRatio() {
        aspectRatioLocked = !aspectRatioLocked;
        const btn = document.getElementById('btn-lock-aspect');
        btn.style.backgroundColor = aspectRatioLocked ? '#e5e7eb' : '';
        btn.textContent = aspectRatioLocked ? '🔒' : '🔓';
    }
    
    function handleAspectRatio(e) {
        if (!aspectRatioLocked) return;
        
        const obj = e.target;
        const originalWidth = obj.width * obj.scaleX;
        const originalHeight = obj.height * obj.scaleY;
        const ratio = originalWidth / originalHeight;
        
        // Maintain aspect ratio during scaling
        if (obj.scaleX !== obj.scaleY) {
            const newScale = Math.max(obj.scaleX, obj.scaleY);
            obj.set({
                scaleX: newScale,
                scaleY: newScale
            });
        }
    }
    
    // Layers Panel
    function toggleLayersPanel() {
        const panel = document.getElementById('layers-panel');
        const btn = document.getElementById('btn-layers');
        if (panel.classList.contains('hidden')) {
            lastFocusForLayers = document.activeElement;
            smoothShow(panel);
            btn && btn.setAttribute('aria-expanded','true');
            panel.setAttribute('aria-hidden','false');
            updateLayers();
            // Focus management
            const focusTarget = document.getElementById('btn-close-layers') || panel;
            setTimeout(() => { try { focusTarget.setAttribute('tabindex','-1'); focusTarget.focus(); } catch(e){} }, 10);
            announce('Layers panel opened');
        } else {
            hideLayersPanel();
        }
    }

    function hideLayersPanel(){
        const panel = document.getElementById('layers-panel');
        const btn = document.getElementById('btn-layers');
        smoothHide(panel, { returnFocusTo: btn || lastFocusForLayers });
        btn && btn.setAttribute('aria-expanded','false');
        panel.setAttribute('aria-hidden','true');
        announce('Layers panel closed');
    }
    
    function updateLayers() {
        const layersList = document.getElementById('layers-list');
        const objects = fabricCanvas.getObjects().slice().reverse(); // Reverse for stacking order
        
        layersList.innerHTML = '';
        
        objects.forEach((obj, index) => {
            const layerItem = createLayerItem(obj, objects.length - index - 1);
            layersList.appendChild(layerItem);
        });
    }
    
    function createLayerItem(obj, actualIndex) {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-800 rounded hover:bg-gray-100 dark:hover:bg-gray-700';
        div.dataset.objectId = obj.id || ('obj_' + actualIndex);
        
        const isSelected = fabricCanvas.getActiveObject() === obj;
        if (isSelected) {
            div.classList.add('ring-2', 'ring-blue-500');
        }
        
        // Layer thumbnail/icon
        const icon = document.createElement('span');
        icon.className = 'text-sm';
        icon.textContent = getObjectIcon(obj);
        
        // Layer name
        const name = document.createElement('input');
        name.type = 'text';
        name.value = obj.name || getObjectName(obj);
        name.className = 'flex-1 bg-transparent text-sm border-none focus:outline-none focus:bg-white dark:focus:bg-gray-900 rounded px-1';
        name.addEventListener('change', (e) => {
            obj.name = e.target.value;
        });
        
        // Visibility toggle
        const visibilityBtn = document.createElement('button');
        visibilityBtn.className = 'text-sm hover:bg-gray-200 dark:hover:bg-gray-600 p-1 rounded';
        visibilityBtn.textContent = obj.visible !== false ? '👁️' : '🚫';
        visibilityBtn.title = 'Toggle Visibility';
        visibilityBtn.addEventListener('click', () => {
            obj.set('visible', obj.visible === false);
            fabricCanvas.requestRenderAll();
            updateLayers();
        });
        
        // Lock toggle
        const lockBtn = document.createElement('button');
        lockBtn.className = 'text-sm hover:bg-gray-200 dark:hover:bg-gray-600 p-1 rounded';
        lockBtn.textContent = obj.selectable === false ? '🔒' : '🔓';
        lockBtn.title = 'Toggle Lock';
        lockBtn.addEventListener('click', () => {
            obj.set({
                selectable: obj.selectable !== false,
                evented: obj.evented !== false
            });
            updateLayers();
        });
        
        // Layer controls
        const controls = document.createElement('div');
        controls.className = 'flex gap-1';
        
        const upBtn = document.createElement('button');
        upBtn.textContent = '↑';
        upBtn.className = 'text-xs px-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded';
        upBtn.title = 'Bring Forward';
        upBtn.addEventListener('click', () => {
            fabricCanvas.bringForward(obj);
            updateLayers();
        });
        
        const downBtn = document.createElement('button');
        downBtn.textContent = '↓';
        downBtn.className = 'text-xs px-1 hover:bg-gray-200 dark:hover:bg-gray-600 rounded';
        downBtn.title = 'Send Backward';
        downBtn.addEventListener('click', () => {
            fabricCanvas.sendBackwards(obj);
            updateLayers();
        });
        
        controls.appendChild(upBtn);
        controls.appendChild(downBtn);
        
        // Click to select
        div.addEventListener('click', (e) => {
            if (e.target === div || e.target === icon) {
                fabricCanvas.setActiveObject(obj);
                fabricCanvas.requestRenderAll();
                updateLayers();
            }
        });
        
        div.appendChild(icon);
        div.appendChild(name);
        div.appendChild(visibilityBtn);
        div.appendChild(lockBtn);
        div.appendChild(controls);
        
        return div;
    }
    
    function getObjectIcon(obj) {
        switch(obj.type) {
            case 'i-text':
            case 'text': return '📝';
            case 'image': return '🖼️';
            case 'rect': return '⬛';
            case 'circle': return '⭕';
            case 'triangle': return '🔺';
            case 'line': return '📏';
            case 'group': return '📦';
            default: return '🎨';
        }
    }
    
    function getObjectName(obj) {
        const type = obj.type.charAt(0).toUpperCase() + obj.type.slice(1);
        if (obj.type === 'i-text' || obj.type === 'text') {
            return obj.text ? obj.text.substring(0, 20) : 'Text';
        }
        return type;
    }
    
    // Export Functions
    function exportAs(e) {
        const format = e.target.value;
        if (!format) return;
        
        fabricCanvas.discardActiveObject();
        fabricCanvas.requestRenderAll();
        
        switch(format) {
            case 'png':
                exportPNG();
                break;
            case 'svg':
                exportSVG();
                break;
            case 'pdf':
                exportPDF();
                break;
        }
        
        e.target.value = '';
    }
    
    function exportPNG() {
        const dataURL = fabricCanvas.toDataURL({ 
            format: 'png', 
            quality: 1.0, 
            multiplier: 2,
            enableRetinaScaling: true
        });
        downloadFile(dataURL, 'design.png');
    }
    
    function exportSVG() {
        const svg = fabricCanvas.toSVG();
        const blob = new Blob([svg], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        downloadFile(url, 'design.svg');
        URL.revokeObjectURL(url);
    }
    
    function exportPDF() {
        if (typeof jsPDF === 'undefined') {
            alert('PDF export is not available. Please check if jsPDF is loaded.');
            return;
        }
        
        const { jsPDF } = window.jspdf;
        const pdf = new jsPDF({
            orientation: fabricCanvas.width > fabricCanvas.height ? 'landscape' : 'portrait',
            unit: 'px',
            format: [fabricCanvas.width, fabricCanvas.height]
        });
        
        const dataURL = fabricCanvas.toDataURL({ 
            format: 'png', 
            quality: 1.0, 
            multiplier: 2
        });
        
        pdf.addImage(dataURL, 'PNG', 0, 0, fabricCanvas.width, fabricCanvas.height);
        pdf.save('design.pdf');
    }
    
    function downloadFile(url, filename) {
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Save/Load Project
    function saveProject() {
        const projectData = {
            version: '1.0',
            canvas: fabricCanvas.toJSON(['id', 'name', 'selectable', 'hasControls']),
            metadata: {
                created: new Date().toISOString(),
                canvasSize: {
                    width: fabricCanvas.getWidth(),
                    height: fabricCanvas.getHeight()
                }
            }
        };
        
        const blob = new Blob([JSON.stringify(projectData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        downloadFile(url, 'design-project.json');
        URL.revokeObjectURL(url);
        
        // Also save to localStorage for autosave
        localStorage.setItem('fabricjs_autosave', JSON.stringify(projectData));
    }
    
    function loadProject(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function(event) {
            try {
                const projectData = JSON.parse(event.target.result);
                
                if (projectData.canvas) {
                    fabricCanvas.loadFromJSON(projectData.canvas, () => {
                        fabricCanvas.requestRenderAll();
                        updateLayers();
                        updatePanels();
                        // Reset history
                        history = [];
                        historyIndex = -1;
                        saveState();
                    });
                }
            } catch (error) {
                alert('Error loading project file: ' + error.message);
            }
        };
        reader.readAsText(file);
        e.target.value = '';
    }
    
    // Autosave
    function startAutosave() {
        autosaveInterval = setInterval(() => {
            const projectData = {
                version: '1.0',
                canvas: fabricCanvas.toJSON(['id', 'name', 'selectable', 'hasControls']),
                timestamp: Date.now()
            };
            localStorage.setItem('fabricjs_autosave', JSON.stringify(projectData));
        }, 30000); // Autosave every 30 seconds
    }
    
    function loadAutosave() {
        try {
            const saved = localStorage.getItem('fabricjs_autosave');
            if (saved) {
                const projectData = JSON.parse(saved);
                if (projectData.canvas && confirm('Found autosaved work. Do you want to restore it?')) {
                    fabricCanvas.loadFromJSON(projectData.canvas, () => {
                        fabricCanvas.requestRenderAll();
                        updateLayers();
                        saveState();
                    });
                }
            }
        } catch (error) {
            console.error('Error loading autosave:', error);
        }
    }
    
    // UI Updates
    function updateUI() {
        // Update history buttons
        const undoBtn = document.getElementById('btn-undo');
        const redoBtn = document.getElementById('btn-redo');
        
        undoBtn.disabled = historyIndex <= 0;
        redoBtn.disabled = historyIndex >= history.length - 1;
        
        undoBtn.style.opacity = undoBtn.disabled ? '0.5' : '1';
        redoBtn.style.opacity = redoBtn.disabled ? '0.5' : '1';
        
        // Update selection-dependent buttons
        const obj = fabricCanvas.getActiveObject();
        const hasSelection = !!obj;
        const hasMultipleSelection = obj && obj.type === 'activeSelection' && obj.size() > 1;
        const hasGroup = obj && obj.type === 'group';
        
        document.getElementById('btn-copy').style.opacity = hasSelection ? '1' : '0.5';
        document.getElementById('btn-delete').style.opacity = hasSelection ? '1' : '0.5';
        document.getElementById('btn-group').style.opacity = hasMultipleSelection ? '1' : '0.5';
        document.getElementById('btn-ungroup').style.opacity = hasGroup ? '1' : '0.5';
        document.getElementById('align-select').style.opacity = hasSelection ? '1' : '0.5';
        document.getElementById('color-picker').style.opacity = hasSelection ? '1' : '0.5';
    }
    
    // Utility Functions
    function getObjectBounds(obj) {
        const bounds = obj.getBoundingRect();
        return {
            left: bounds.left,
            top: bounds.top,
            width: bounds.width,
            height: bounds.height
        };
    }
    
    function snapToObjects(movingObj, threshold = 5) {
        const movingBounds = getObjectBounds(movingObj);
        const objects = fabricCanvas.getObjects().filter(obj => obj !== movingObj);
        
        let snapX = null, snapY = null;
        
        objects.forEach(obj => {
            const bounds = getObjectBounds(obj);
            
            // Horizontal snapping
            if (Math.abs(movingBounds.left - bounds.left) < threshold) {
                snapX = bounds.left;
            } else if (Math.abs(movingBounds.left - (bounds.left + bounds.width)) < threshold) {
                snapX = bounds.left + bounds.width;
            } else if (Math.abs((movingBounds.left + movingBounds.width) - bounds.left) < threshold) {
                snapX = bounds.left - movingBounds.width;
            }
            
            // Vertical snapping
            if (Math.abs(movingBounds.top - bounds.top) < threshold) {
                snapY = bounds.top;
            } else if (Math.abs(movingBounds.top - (bounds.top + bounds.height)) < threshold) {
                snapY = bounds.top + bounds.height;
            } else if (Math.abs((movingBounds.top + movingBounds.height) - bounds.top) < threshold) {
                snapY = bounds.top - movingBounds.height;
            }
        });
        
        if (snapX !== null) movingObj.set('left', snapX);
        if (snapY !== null) movingObj.set('top', snapY);
    }
    
    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (autosaveInterval) {
            clearInterval(autosaveInterval);
        }
    });
    
    // Color contrast checker (utility)
    function checkContrast(color1, color2) {
        const rgb1 = hexToRgb(color1);
        const rgb2 = hexToRgb(color2);
        
        const l1 = getLuminance(rgb1);
        const l2 = getLuminance(rgb2);
        
        const contrast = (Math.max(l1, l2) + 0.05) / (Math.min(l1, l2) + 0.05);
        return contrast;
    }
    
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }
    
    function getLuminance(rgb) {
        const { r, g, b } = rgb;
        const [rs, gs, bs] = [r, g, b].map(c => {
            c /= 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        });
        return 0.2126 * rs + 0.7152 * gs + 0.0722 * bs;
    }
    
    // Template functions (preset canvas sizes)
    function setCanvasSize(width, height) {
        fabricCanvas.setWidth(width);
        fabricCanvas.setHeight(height);
        fabricCanvas.requestRenderAll();
        setupGrid();
    }
    
    // Quick templates
    const templates = {
        'A4': { width: 794, height: 1123 },
        'Letter': { width: 816, height: 1056 },
        'Instagram Post': { width: 1080, height: 1080 },
        'Instagram Story': { width: 1080, height: 1920 },
        'Facebook Cover': { width: 1640, height: 859 },
        'Twitter Header': { width: 1500, height: 500 }
    };
    
    // Add template selector (if needed in future)
    function addTemplateSelector() {
        const select = document.createElement('select');
        select.className = 'px-3 py-2 bg-gray-600 text-white text-sm rounded hover:bg-gray-700';
        select.innerHTML = '<option value="">Templates</option>';
        
        Object.entries(templates).forEach(([name, size]) => {
            const option = document.createElement('option');
            option.value = JSON.stringify(size);
            option.textContent = name;
            select.appendChild(option);
        });
        
        select.addEventListener('change', (e) => {
            if (e.target.value) {
                const size = JSON.parse(e.target.value);
                if (confirm(`Change canvas size to ${size.width}x${size.height}?`)) {
                    setCanvasSize(size.width, size.height);
                }
                e.target.value = '';
            }
        });
        
        return select;
    }

})();
</script>
<script>
// Button color controls (standalone)
(function() {
    const toolbar = document.getElementById('editor-toolbar');
    if (!toolbar) return;

    const textPicker = document.getElementById('btn-text-color-picker');
    const bgPicker = document.getElementById('btn-bg-color-picker');
    const resetBtn = document.getElementById('btn-reset-colors');

    function applyButtonColors(textColor, bgColor) {
        const btnNodes = toolbar.querySelectorAll('button, label, select, input[type="color"].overrideable');
        btnNodes.forEach(el => {
            if (el.id === 'btn-text-color-picker' || el.id === 'btn-bg-color-picker' || el.id === 'btn-reset-colors') return;
            if (el.tagName.toLowerCase() === 'select') {
                el.style.color = textColor;
                el.style.backgroundColor = bgColor;
            } else if (el.tagName.toLowerCase() === 'label') {
                el.style.color = textColor;
            } else {
                el.style.color = textColor;
                el.style.backgroundColor = bgColor;
                el.style.borderColor = darkenColor(bgColor, 10);
            }
        });
        try {
            localStorage.setItem('editor_btn_text_color', textColor);
            localStorage.setItem('editor_btn_bg_color', bgColor);
        } catch (err) { /* ignore */ }
    }

    function darkenColor(hex, percent) {
        if (!hex) return hex;
        const num = parseInt(hex.replace('#',''),16);
        const amt = Math.round(2.55 * percent);
        let R = (num >> 16) - amt;
        let G = ((num >> 8) & 0x00FF) - amt;
        let B = (num & 0x0000FF) - amt;
        R = (R < 0) ? 0 : (R > 255) ? 255 : R;
        G = (G < 0) ? 0 : (G > 255) ? 255 : G;
        B = (B < 0) ? 0 : (B > 255) ? 255 : B;
        return '#' + (R.toString(16).padStart(2,'0')) + (G.toString(16).padStart(2,'0')) + (B.toString(16).padStart(2,'0'));
    }

    const savedText = localStorage.getItem('editor_btn_text_color');
    const savedBg = localStorage.getItem('editor_btn_bg_color');
    if (savedText) textPicker.value = savedText;
    if (savedBg) bgPicker.value = savedBg;

    applyButtonColors(textPicker.value, bgPicker.value);

    textPicker.addEventListener('input', () => {
        applyButtonColors(textPicker.value, bgPicker.value);
    });
    bgPicker.addEventListener('input', () => {
        applyButtonColors(textPicker.value, bgPicker.value);
    });

    resetBtn.addEventListener('click', () => {
        const defaultText = '#ffffff';
        const defaultBg = '#2563eb';
        textPicker.value = defaultText;
        bgPicker.value = defaultBg;
        applyButtonColors(defaultText, defaultBg);
    });
})();
</script>

@endsection
