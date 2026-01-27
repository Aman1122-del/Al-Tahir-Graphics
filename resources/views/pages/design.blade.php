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
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800">
    <div class="max-w-[100rem] mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white dark:bg-gray-900 shadow-xl rounded-2xl overflow-hidden">
            <!-- Header Section -->
            <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Design Studio</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create professional designs with our powerful editor</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Quick Actions -->
                        <button id="btn-save-design" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Save Design
                        </button>
                        <button id="btn-add-to-cart" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:scale-105 transform">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6m8 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Main Editor Layout: Left toolbar, Center canvas, Right properties -->
            <div class="flex flex-col lg:flex-row min-h-[calc(100vh-12rem)] bg-gray-50 dark:bg-gray-800/50">
            <!-- Left Sidebar Toolbar -->
            <aside class="lg:w-72 shrink-0 bg-white dark:bg-gray-900 border-r lg:border-r border-b lg:border-b-0 border-gray-200 dark:border-gray-700 p-4 lg:p-6 lg:overflow-y-auto" role="complementary" aria-label="Editor tools">
                <!-- Mobile toggle button -->
                <button id="mobile-toolbar-toggle" class="lg:hidden w-full mb-4 px-4 py-2 bg-blue-600 text-white rounded-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    Tools
                </button>
                
                <!-- Collapsible toolbar content on mobile -->
                <div id="mobile-toolbar-content" class="lg:block">
                <nav id="editor-toolbar" class="space-y-6" role="toolbar" aria-label="Editor toolbar">
                    <!-- Button Color Controls -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Theme Colors</h3>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Text Color</label>
                                <input id="btn-text-color-picker" type="color" class="w-full h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:border-blue-500 transition-colors" value="#ffffff" title="Button text color">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Background</label>
                                <input id="btn-bg-color-picker" type="color" class="w-full h-10 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:border-blue-500 transition-colors" value="#2563eb" title="Button background color">
                            </div>
                        </div>
                        <button id="btn-reset-colors" class="mt-3 w-full px-3 py-2 text-xs font-medium text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors" title="Reset button colors">Reset Colors</button>
                    </div>

                    <!-- Primary Tools -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Selection Tools</h3>
                        <div class="grid grid-cols-3 gap-2">
                            <button id="tool-select" class="flex flex-col items-center p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" aria-pressed="true" aria-label="Select tool">
                                <span class="text-lg mb-1">🖱️</span>
                                <span class="text-xs font-medium">Select</span>
                            </button>
                            <button id="tool-move" class="flex flex-col items-center p-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 hover:scale-105 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" aria-pressed="false" aria-label="Move tool">
                                <span class="text-lg mb-1">✥</span>
                                <span class="text-xs font-medium">Move</span>
                            </button>
                            <button id="tool-brush" class="flex flex-col items-center p-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-all duration-200 hover:scale-105 focus:ring-2 focus:ring-gray-500 focus:ring-offset-2" aria-pressed="false" aria-label="Brush tool">
                                <span class="text-lg mb-1">🖌️</span>
                                <span class="text-xs font-medium">Brush</span>
                            </button>
                        </div>
                    </div>

                    <!-- Add Elements -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Add Elements</h3>
                        <div class="space-y-3">
                            <button id="btn-add-text" class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" aria-label="Add Text">
                                <span class="mr-2">📝</span>
                                Add Text
                            </button>
                            
                            <div class="relative">
                                <label for="image-upload" class="w-full inline-flex items-center justify-center px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 cursor-pointer transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" aria-label="Upload Image">
                                    <span class="mr-2">🖼️</span>
                                    Upload Image
                                </label>
                                <input id="image-upload" type="file" accept="image/*" class="hidden" />
                                <!-- Image preview will appear here -->
                                <div id="image-preview" class="hidden mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded border-2 border-dashed border-gray-300 dark:border-gray-600">
                                    <img id="preview-thumbnail" class="w-full h-20 object-cover rounded" alt="Preview">
                                    <div class="flex items-center justify-center mt-2">
                                        <svg class="w-5 h-5 text-green-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="ml-1 text-xs text-green-600 dark:text-green-400">Ready to use</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <select id="shape-select" class="w-full px-4 py-3 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 appearance-none cursor-pointer" aria-label="Add Shape">
                                    <option value="" class="bg-gray-800">➕ Add Shape</option>
                                    <option value="rectangle" class="bg-gray-800">⬛ Rectangle</option>
                                    <option value="circle" class="bg-gray-800">⭕ Circle</option>
                                    <option value="triangle" class="bg-gray-800">🔺 Triangle</option>
                                    <option value="line" class="bg-gray-800">📏 Line</option>
                                    <option value="arrow" class="bg-gray-800">➡️ Arrow</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- History & Edit Controls -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Edit Controls</h3>
                        <div class="grid grid-cols-2 gap-2">
                            <button id="btn-undo" class="flex items-center justify-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Undo" disabled>
                                <span class="mr-1">↶</span>
                                Undo
                            </button>
                            <button id="btn-redo" class="flex items-center justify-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed" aria-label="Redo" disabled>
                                <span class="mr-1">↷</span>
                                Redo
                            </button>
                        </div>
                    </div>

                    <!-- Group & Align Controls -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Object Controls</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <button id="btn-group" class="flex items-center justify-center px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" aria-label="Group Objects">
                                    <span class="mr-1">📦</span>
                                    Group
                                </button>
                                <button id="btn-ungroup" class="flex items-center justify-center px-3 py-2 bg-orange-600 text-white text-sm rounded-lg hover:bg-orange-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2" aria-label="Ungroup Objects">
                                    <span class="mr-1">📤</span>
                                    Ungroup
                                </button>
                            </div>
                            <div class="relative">
                                <select id="align-select" class="w-full px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 appearance-none cursor-pointer" aria-label="Align Objects">
                                    <option value="" class="bg-gray-800">⚹ Align Objects</option>
                                    <option value="left" class="bg-gray-800">← Left</option>
                                    <option value="center" class="bg-gray-800">↔ Center H</option>
                                    <option value="right" class="bg-gray-800">→ Right</option>
                                    <option value="top" class="bg-gray-800">↑ Top</option>
                                    <option value="middle" class="bg-gray-800">↕ Middle V</option>
                                    <option value="bottom" class="bg-gray-800">↓ Bottom</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- View Controls -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">View Controls</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <button id="btn-zoom-out" class="flex-1 flex items-center justify-center px-3 py-2 bg-slate-600 text-white text-sm rounded-lg hover:bg-slate-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2" aria-label="Zoom Out">
                                    🔍-
                                </button>
                                <div id="zoom-level" class="px-3 py-2 text-sm font-medium bg-slate-100 dark:bg-slate-700 text-gray-900 dark:text-white rounded-lg min-w-[4rem] text-center">100%</div>
                                <button id="btn-zoom-in" class="flex-1 flex items-center justify-center px-3 py-2 bg-slate-600 text-white text-sm rounded-lg hover:bg-slate-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2" aria-label="Zoom In">
                                    🔍+
                                </button>
                            </div>
                            <button id="btn-fit" class="w-full flex items-center justify-center px-3 py-2 bg-slate-600 text-white text-sm rounded-lg hover:bg-slate-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2" aria-label="Fit to Screen">
                                <span class="mr-2">⊞</span>
                                Fit to Screen
                            </button>
                        </div>
                    </div>

                    <!-- Grid & Panels -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Panel Controls</h3>
                        <div class="space-y-2">
                            <button id="btn-grid" class="w-full flex items-center justify-center px-3 py-2 bg-teal-600 text-white text-sm rounded-lg hover:bg-teal-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2" aria-label="Toggle Grid">
                                <span class="mr-2">🔲</span>
                                Toggle Grid
                            </button>
                            <button id="btn-layers" class="w-full flex items-center justify-center px-3 py-2 bg-cyan-600 text-white text-sm rounded-lg hover:bg-cyan-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2" aria-label="Toggle Layers Panel">
                                <span class="mr-2">📋</span>
                                Layers Panel
                            </button>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Quick Actions</h3>
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Fill Color</label>
                                <input id="color-picker" type="color" class="flex-1 h-10 p-1 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer hover:border-blue-500 transition-colors" title="Fill Color" aria-label="Change Fill Color" />
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button id="btn-copy" class="flex items-center justify-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" aria-label="Copy Object">
                                    <span class="mr-1">📋</span>
                                    Copy
                                </button>
                                <button id="btn-delete" class="flex items-center justify-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2" aria-label="Delete Selected">
                                    <span class="mr-1">🗑️</span>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Canvas Settings -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Canvas Settings</h3>
                        <div class="space-y-3">
                            <div class="relative">
                                <select id="canvas-preset" class="w-full px-4 py-3 bg-slate-600 text-white text-sm rounded-lg hover:bg-slate-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 appearance-none cursor-pointer" aria-label="Canvas size preset">
                                    <option value="" class="bg-gray-800">📐 Canvas Presets</option>
                                    <option value="A4" data-width="794" data-height="1123" class="bg-gray-800">📄 A4 (794×1123)</option>
                                    <option value="Letter" data-width="816" data-height="1056" class="bg-gray-800">📄 Letter (816×1056)</option>
                                    <option value="Instagram Post" data-width="1080" data-height="1080" class="bg-gray-800">📱 Instagram Post (1080×1080)</option>
                                    <option value="Instagram Story" data-width="1080" data-height="1920" class="bg-gray-800">📱 Instagram Story (1080×1920)</option>
                                    <option value="Facebook Cover" data-width="1640" data-height="859" class="bg-gray-800">🌐 Facebook Cover (1640×859)</option>
                                    <option value="Twitter Header" data-width="1500" data-height="500" class="bg-gray-800">🐦 Twitter Header (1500×500)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2 items-end">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400" for="canvas-width-input">Width</label>
                                    <input id="canvas-width-input" type="number" min="100" step="1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Width" aria-label="Canvas width">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400" for="canvas-height-input">Height</label>
                                    <input id="canvas-height-input" type="number" min="100" step="1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Height" aria-label="Canvas height">
                                </div>
                                <button id="btn-apply-canvas-size" class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2" aria-label="Apply canvas size">Apply</button>
                            </div>
                        </div>
                    </div>

                    <!-- Project & Export -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Project & Export</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-2 gap-2">
                                <button id="btn-save" class="flex items-center justify-center px-3 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" aria-label="Save Project">
                                    <span class="mr-1">💾</span>
                                    Save
                                </button>
                                <label for="load-project" class="flex items-center justify-center px-3 py-2 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 cursor-pointer transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2" aria-label="Load Project">
                                    <span class="mr-1">📂</span>
                                    Load
                                </label>
                                <input id="load-project" type="file" accept=".json" class="hidden" />
                            </div>
                            <div class="relative">
                                <select id="export-select" class="w-full px-4 py-3 bg-emerald-600 text-white text-sm rounded-lg hover:bg-emerald-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 appearance-none cursor-pointer" aria-label="Export Options">
                                    <option value="" class="bg-gray-800">⬇️ Export As</option>
                                    <option value="png" class="bg-gray-800">🖼️ PNG Image</option>
                                    <option value="svg" class="bg-gray-800">📐 SVG Vector</option>
                                    <option value="pdf" class="bg-gray-800">📄 PDF Document</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
                </div>
            </aside>

            <!-- Center Canvas -->
            <main class="flex-1 flex  justify-center p-4 lg:p-6 bg-white dark:bg-gray-900">
                <div class="relative max-w-4xl w-full">
                    <!-- Canvas Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Canvas</h2>
                            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>•</span>
                                <span id="canvas-info">Click to start designing</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Quick zoom controls -->
                            <button class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors" title="Zoom to fit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Canvas Container with enhanced styling -->
                    <div class="relative bg-gray-100 dark:bg-gray-800 rounded-2xl p-8 shadow-inner transition-all duration-300 hover:shadow-lg">
                        <div class="relative bg-white rounded-xl shadow-lg overflow-hidden transition-all duration-200 hover:shadow-xl hover:scale-[1.01]" style="aspect-ratio: 4/3;">
                            <canvas id="design-canvas" class="block w-full h-full transition-opacity duration-200"></canvas>
                            <canvas id="grid-canvas" class="absolute top-0 left-0 pointer-events-none hidden transition-opacity duration-200"></canvas>

                            
                            <!-- Canvas overlay for loading state -->
                            <div id="canvas-loading" class="hidden absolute inset-0 bg-white/80 dark:bg-gray-900/80 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Loading...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Canvas corners for resize handles (visual only) -->
                        <div class="absolute top-6 left-6 w-4 h-4 border-t-2 border-l-2 border-gray-300 dark:border-gray-600 rounded-tl"></div>
                        <div class="absolute top-6 right-6 w-4 h-4 border-t-2 border-r-2 border-gray-300 dark:border-gray-600 rounded-tr"></div>
                        <div class="absolute bottom-6 left-6 w-4 h-4 border-b-2 border-l-2 border-gray-300 dark:border-gray-600 rounded-bl"></div>
                        <div class="absolute bottom-6 right-6 w-4 h-4 border-b-2 border-r-2 border-gray-300 dark:border-gray-600 rounded-br"></div>
                    </div>
                </div>
            </main>

            <!-- Right Properties Panel -->
            <aside class="lg:w-80 shrink-0 bg-white dark:bg-gray-900 border-l lg:border-l border-t lg:border-t-0 border-gray-200 dark:border-gray-700 p-4 lg:p-6 lg:overflow-y-auto order-last lg:order-none" role="complementary" aria-label="Properties panel">
                <!-- Mobile toggle for properties -->
                <button id="mobile-properties-toggle" class="lg:hidden w-full mb-4 px-4 py-2 bg-purple-600 text-white rounded-lg flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Properties
                </button>
                
                <!-- Collapsible properties content on mobile -->
                <div id="mobile-properties-content" class="lg:block">
                <!-- Panel Header -->
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Properties</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Customize selected objects</p>
                </div>
                <!-- Enhanced Text Formatting Panel -->
                <div id="text-panel" class="hidden space-y-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Typography</h3>
                        <div class="space-y-3">
                            <!-- Font and Size Row -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Font Family</label>
                                    <select id="font-family" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Font Family">
                                        <option value="Arial">Arial</option>
                                        <option value="Helvetica">Helvetica</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Courier New">Courier New</option>
                                        <option value="Impact">Impact</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Font Size</label>
                                    <input id="font-size" type="number" min="8" max="200" value="24" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Size" aria-label="Font Size">
                                </div>
                            </div>
                            
                            <!-- Line Height and Letter Spacing -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Line Height</label>
                                    <input id="line-height" type="number" min="0.5" max="3" step="0.1" value="1.2" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Line Height" aria-label="Line Height">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Letter Spacing</label>
                                    <input id="letter-spacing" type="number" min="-10" max="50" step="0.1" value="0" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Letter Spacing" aria-label="Letter Spacing">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Formatting</h3>
                        <div class="space-y-3">
                            <!-- Style Buttons -->
                            <div class="flex gap-2">
                                <button id="btn-bold" class="flex-1 flex items-center justify-center px-3 py-2 text-sm font-bold rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-200" aria-label="Bold">
                                    <span class="font-bold">B</span>
                                </button>
                                <button id="btn-italic" class="flex-1 flex items-center justify-center px-3 py-2 text-sm italic rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-200" aria-label="Italic">
                                    <span class="italic">I</span>
                                </button>
                                <button id="btn-underline" class="flex-1 flex items-center justify-center px-3 py-2 text-sm underline rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-200" aria-label="Underline">
                                    <span class="underline">U</span>
                                </button>
                            </div>
                            
                            <!-- Alignment and Case -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Text Align</label>
                                    <select id="text-align" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Text Alignment">
                                        <option value="left">← Left</option>
                                        <option value="center">↔ Center</option>
                                        <option value="right">→ Right</option>
                                        <option value="justify">↕ Justify</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Text Case</label>
                                    <select id="text-case" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Text Case">
                                        <option value="">Normal</option>
                                        <option value="uppercase">UPPERCASE</option>
                                        <option value="lowercase">lowercase</option>
                                        <option value="capitalize">Title Case</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Color -->
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Text Color</label>
                                <input id="text-color" type="color" class="w-full h-10 p-1 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:border-blue-500 transition-colors" title="Text Color" aria-label="Text Color">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Style Panel -->
                <div id="style-panel" class="hidden space-y-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Appearance</h3>
                        <div class="space-y-3">
                            <!-- Stroke and Fill -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Stroke Color</label>
                                    <input id="stroke-color" type="color" class="w-full h-10 p-1 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:border-blue-500 transition-colors" title="Stroke Color" aria-label="Stroke Color">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Stroke Width</label>
                                    <input id="stroke-width" type="number" min="0" max="20" step="1" value="0" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Width" aria-label="Stroke Width">
                                </div>
                            </div>
                            
                            <!-- Opacity -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Opacity</label>
                                    <span class="text-xs font-medium text-gray-900 dark:text-white"><span id="opacity-value">100</span>%</span>
                                </div>
                                <input id="object-opacity" type="range" min="0" max="100" value="100" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700 slider" aria-label="Opacity">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Shadow</h3>
                        <div class="space-y-3">
                            <div class="grid grid-cols-3 gap-2">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">X</label>
                                    <input id="shadow-x" type="number" min="-50" max="50" value="0" class="w-full px-2 py-2 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="X" aria-label="Shadow X">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Y</label>
                                    <input id="shadow-y" type="number" min="-50" max="50" value="0" class="w-full px-2 py-2 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Y" aria-label="Shadow Y">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Blur</label>
                                    <input id="shadow-blur" type="number" min="0" max="50" value="0" class="w-full px-2 py-2 text-xs rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Blur" aria-label="Shadow Blur">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Shadow Color</label>
                                <input id="shadow-color" type="color" class="w-full h-10 p-1 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:border-blue-500 transition-colors" title="Shadow Color" aria-label="Shadow Color">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transform Panel -->
                <div id="transform-panel" class="hidden space-y-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Position & Size</h3>
                        <div class="space-y-3">
                            <!-- Position -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">X Position</label>
                                    <input id="pos-x" type="number" step="0.1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="X Position">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Y Position</label>
                                    <input id="pos-y" type="number" step="0.1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Y Position">
                                </div>
                            </div>
                            
                            <!-- Size -->
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Width</label>
                                    <input id="width" type="number" step="0.1" min="1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Width">
                                </div>
                                <div class="space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Height</label>
                                    <input id="height" type="number" step="0.1" min="1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Height">
                                </div>
                            </div>
                            
                            <!-- Rotation and Lock -->
                            <div class="flex gap-3">
                                <div class="flex-1 space-y-1">
                                    <label class="text-xs font-medium text-gray-600 dark:text-gray-400">Rotation (°)</label>
                                    <input id="rotation" type="number" step="1" class="w-full px-3 py-2 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" aria-label="Rotation">
                                </div>
                                <div class="flex flex-col justify-end">
                                    <button id="btn-lock-aspect" class="flex items-center justify-center w-10 h-10 text-lg rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-2 focus:ring-blue-500 transition-all duration-200" title="Lock Aspect Ratio" aria-label="Lock Aspect Ratio">
                                        🔓
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Layers Panel (moved to properties area) -->
                <div id="layers-panel" class="hidden space-y-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Layers</h3>
                        <button id="btn-close-layers" class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors" aria-label="Close Layers Panel">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="layers-list" class="space-y-2 max-h-80 overflow-y-auto">
                        <!-- Layers will be populated here -->
                        <div class="text-center py-8 text-gray-500 dark:text-gray-400 text-sm">
                            <svg class="w-8 h-8 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Add elements to see layers
                        </div>
                    </div>
                </div>
                </div>
            </aside>
        </div>

        <!-- Footer Status Bar -->
        <div id="editor-statusbar" class="border-t border-gray-200 dark:border-gray-700 px-6 py-4 bg-white dark:bg-gray-900" role="status" aria-live="polite" aria-atomic="true">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Tool:</span>
                        <span id="status-tool" class="font-medium text-gray-900 dark:text-white">Select</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Canvas:</span>
                        <span id="status-canvas-size" class="font-medium text-gray-900 dark:text-white">—</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Zoom:</span>
                        <span id="status-zoom" class="font-medium text-gray-900 dark:text-white">—</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-500 dark:text-gray-400">Selection:</span>
                        <span id="status-selection" class="font-medium text-gray-900 dark:text-white">None</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                    <span class="hidden sm:inline">Press ? for help</span>
                    <button class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors" title="Keyboard shortcuts">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Instructions (Collapsible on mobile) -->
        <div class="border-t border-gray-200 dark:border-gray-700 px-6 py-3 bg-gray-50 dark:bg-gray-800">
            <details class="text-sm text-gray-600 dark:text-gray-400">
                <summary class="cursor-pointer hover:text-gray-900 dark:hover:text-white transition-colors font-medium">Keyboard Shortcuts</summary>
                <div class="mt-2 space-y-1">
                    <p><strong>History:</strong> Ctrl+Z (undo), Ctrl+Y (redo)</p>
                    <p><strong>Clipboard:</strong> Ctrl+C (copy), Ctrl+V (paste), Ctrl+X (cut)</p>
                    <p><strong>Grouping:</strong> Ctrl+G (group), Ctrl+Shift+G (ungroup)</p>
                    <p><strong>Selection:</strong> Ctrl+A (select all), Delete (delete selected)</p>
                    <p><strong>Movement:</strong> Arrow keys (nudge), Shift+Arrow (large nudge)</p>
                    <p><strong>View:</strong> Ctrl+Mousewheel (zoom), Space+Drag (pan)</p>
                </div>
            </details>
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

<!-- Enhanced UI/UX JavaScript -->
<script>
// Mobile responsiveness and micro-interactions
(function() {
    'use strict';
    
    // Mobile toolbar toggle
    const mobileToolbarToggle = document.getElementById('mobile-toolbar-toggle');
    const mobileToolbarContent = document.getElementById('mobile-toolbar-content');
    
    if (mobileToolbarToggle && mobileToolbarContent) {
        // Initially hidden on mobile
        if (window.innerWidth < 1024) {
            mobileToolbarContent.classList.add('hidden');
        }
        
        mobileToolbarToggle.addEventListener('click', function() {
            mobileToolbarContent.classList.toggle('hidden');
            
            // Update button icon
            const icon = this.querySelector('svg');
            if (mobileToolbarContent.classList.contains('hidden')) {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>';
            } else {
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
            }
        });
    }
    
    // Mobile properties toggle
    const mobilePropertiesToggle = document.getElementById('mobile-properties-toggle');
    const mobilePropertiesContent = document.getElementById('mobile-properties-content');
    
    if (mobilePropertiesToggle && mobilePropertiesContent) {
        // Initially hidden on mobile
        if (window.innerWidth < 1024) {
            mobilePropertiesContent.classList.add('hidden');
        }
        
        mobilePropertiesToggle.addEventListener('click', function() {
            mobilePropertiesContent.classList.toggle('hidden');
        });
    }
    
    // Image upload preview enhancement
    const imageUpload = document.getElementById('image-upload');
    const imagePreview = document.getElementById('image-preview');
    const previewThumbnail = document.getElementById('preview-thumbnail');
    
    if (imageUpload && imagePreview && previewThumbnail) {
        imageUpload.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    previewThumbnail.src = event.target.result;
                    imagePreview.classList.remove('hidden');
                    
                    // Add success animation
                    setTimeout(() => {
                        const checkIcon = imagePreview.querySelector('svg');
                        if (checkIcon) {
                            checkIcon.classList.add('animate-bounce');
                            setTimeout(() => {
                                checkIcon.classList.remove('animate-bounce');
                            }, 1000);
                        }
                    }, 100);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Add to Cart button functionality
    const addToCartBtn = document.getElementById('btn-add-to-cart');
    const saveDesignBtn = document.getElementById('btn-save-design');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            // Show loading state
            const originalContent = this.innerHTML;
            this.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                </svg>
                Adding...
            `;
            this.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Added to Cart!
                `;
                this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                this.classList.add('bg-green-600', 'hover:bg-green-700');
                
                // Show success toast
                showToast('Design added to cart successfully!', 'success');
                
                // Reset after 3 seconds
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.classList.remove('bg-green-600', 'hover:bg-green-700');
                    this.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    this.disabled = false;
                }, 3000);
            }, 1500);
        });
    }
    
    if (saveDesignBtn) {
        saveDesignBtn.addEventListener('click', function() {
            // Show loading state
            const originalContent = this.innerHTML;
            this.innerHTML = `
                <svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="3" stroke-width="2"></circle>
                </svg>
                Saving...
            `;
            this.disabled = true;
            
            // Simulate save
            setTimeout(() => {
                this.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Saved!
                `;
                
                showToast('Design saved successfully!', 'success');
                
                // Reset after 2 seconds
                setTimeout(() => {
                    this.innerHTML = originalContent;
                    this.disabled = false;
                }, 2000);
            }, 1000);
        });
    }
    
    // Toast notification system
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 translate-x-full opacity-0`;
        
        switch (type) {
            case 'success':
                toast.classList.add('bg-green-600');
                break;
            case 'error':
                toast.classList.add('bg-red-600');
                break;
            case 'warning':
                toast.classList.add('bg-yellow-600');
                break;
            default:
                toast.classList.add('bg-blue-600');
        }
        
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 100);
        
        // Auto-hide after 4 seconds
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    document.body.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }
    
    // Canvas info updates
    function updateCanvasInfo(message) {
        const canvasInfo = document.getElementById('canvas-info');
        if (canvasInfo) {
            canvasInfo.textContent = message;
            canvasInfo.classList.add('text-blue-600');
            setTimeout(() => {
                canvasInfo.classList.remove('text-blue-600');
            }, 2000);
        }
    }
    
    // Add ripple effect to buttons
    function addRippleEffect(button) {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    }
    
    // Apply ripple effect to main buttons
    document.querySelectorAll('#editor-toolbar button, #btn-add-to-cart, #btn-save-design').forEach(addRippleEffect);
    
    // Enhanced focus management for accessibility
    document.addEventListener('keydown', function(e) {
        // Tab navigation enhancement
        if (e.key === 'Tab') {
            document.body.classList.add('user-is-tabbing');
        }
        
        // ESC to close mobile panels
        if (e.key === 'Escape') {
            if (mobileToolbarContent && !mobileToolbarContent.classList.contains('hidden')) {
                mobileToolbarToggle.click();
            }
            if (mobilePropertiesContent && !mobilePropertiesContent.classList.contains('hidden')) {
                mobilePropertiesToggle.click();
            }
        }
    });
    
    document.addEventListener('mousedown', function() {
        document.body.classList.remove('user-is-tabbing');
    });
    
    // Touch support for mobile
    let touchStartY = 0;
    let touchCurrentY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    document.addEventListener('touchmove', function(e) {
        touchCurrentY = e.touches[0].clientY;
    }, { passive: true });
    
    // Window resize handler for responsive adjustments
    window.addEventListener('resize', function() {
        const isMobile = window.innerWidth < 1024;
        
        if (!isMobile) {
            // Show panels on desktop
            if (mobileToolbarContent) {
                mobileToolbarContent.classList.remove('hidden');
            }
            if (mobilePropertiesContent) {
                mobilePropertiesContent.classList.remove('hidden');
            }
        } else {
            // Reset mobile panels
            if (mobileToolbarContent && !mobileToolbarContent.classList.contains('hidden')) {
                mobileToolbarContent.classList.add('hidden');
            }
            if (mobilePropertiesContent && !mobilePropertiesContent.classList.contains('hidden')) {
                mobilePropertiesContent.classList.add('hidden');
            }
        }
    });
    
    // Initialize canvas info
    updateCanvasInfo('Ready to design');
    
    // Global function for components to trigger updates
    window.updateCanvasInfo = updateCanvasInfo;
    window.showToast = showToast;
})();
</script>

<!-- Add custom CSS for ripple effect -->
<style>
.ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.6);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}

.user-is-tabbing *:focus {
    outline: 2px solid #3b82f6 !important;
    outline-offset: 2px !important;
}

/* Custom slider styling */
.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-moz-range-thumb {
    height: 20px;
    width: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Mobile improvements */
@media (max-width: 1023px) {
    .canvas-container {
        aspect-ratio: 16/9;
    }
    
    #design-canvas {
        touch-action: pan-x pan-y;
    }
}
</style>

@endsection
