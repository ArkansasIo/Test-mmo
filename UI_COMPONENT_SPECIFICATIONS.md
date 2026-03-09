================================================================================
    UI COMPONENT LIBRARY & DESIGN SYSTEM
    OGame 0.84 Sci-Fi Dark Theme
================================================================================

OVERVIEW
================================================================================
Complete UI component specifications for the Sci-Fi Conquest: Awakening game
following OGame 0.84 design standards with modern web practices.

File Location: Index/css/components.css (to be created)
JavaScript: Index/js/components.js (to be created)

================================================================================
SECTION 1: CORE COMPONENTS
================================================================================

1. RESPONSIVE GRID SYSTEM
================================================================================

Container Sizes:
├─ Full width: 100%
├─ Max width: 1600px (large screens)
├─ Padding: 20px sides
└─ Breakpoints: 320px, 768px, 1024px, 1440px, 1920px

Grid Layout:
├─ 12-column grid (standard)
├─ Gap: 20px
├─ Building columns: 4 (desktop), 2 (tablet), 1 (mobile)
└─ Flexbox primary, CSS Grid for complex layouts

Example HTML:
<div class="container">
    <div class="row">
        <div class="col-md-4 col-sm-2 col-xs-1">
            <div class="card">Content</div>
        </div>
    </div>
</div>

CSS:
.container {
    max-width: 1600px;
    margin: 0 auto;
    padding: 0 20px;
}

.row {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 20px;
}

.col-md-4 { grid-column: span 4; }
@media (max-width: 1024px) {
    .col-sm-2 { grid-column: span 2; }
}
@media (max-width: 768px) {
    .col-xs-1 { grid-column: span 1; }
}

2. CARD COMPONENT
================================================================================

Purpose: Container for grouped related content
Uses: Building info, planet stats, fleet status, research progress

Structure:
┌─────────────────────────────────┐
│ ▲ Card Header (Optional)        │
│ ├─ Icon + Title                 │
│ └─ Badge/Status (Optional)      │
├─────────────────────────────────┤
│ Card Body                       │
│ ├─ Main content                 │
│ ├─ Stats/metrics                │
│ └─ Progress bars                │
├─────────────────────────────────┤
│ Card Footer (Optional)          │
│ ├─ Action buttons               │
│ └─ Secondary info               │
└─────────────────────────────────┘

HTML:
<div class="card">
    <div class="card-header">
        <span class="card-icon">🏗️</span>
        <h3 class="card-title">Metal Mine</h3>
        <span class="badge badge-success">Level 5</span>
    </div>
    <div class="card-body">
        <div class="card-stat">
            <span class="stat-label">Production:</span>
            <span class="stat-value">1,200 metal/hr</span>
        </div>
        <div class="progress">
            <div class="progress-bar" style="width: 75%"></div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-sm btn-upgrade">Upgrade</button>
    </div>
</div>

CSS:
.card {
    background: rgba(10, 10, 30, 0.9);
    border: 1px solid #4a9eff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(74, 158, 255, 0.3);
    transform: translateY(-2px);
}

.card-header {
    background: rgba(74, 158, 255, 0.1);
    border-bottom: 1px solid rgba(74, 158, 255, 0.2);
    padding: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-title {
    color: #4a9eff;
    font-size: 16px;
    font-weight: bold;
    flex: 1;
    margin: 0;
}

.card-body {
    padding: 15px;
    color: #ffffff;
}

.card-footer {
    background: rgba(0, 0, 0, 0.3);
    padding: 12px 15px;
    border-top: 1px solid rgba(74, 158, 255, 0.1);
    display: flex;
    gap: 10px;
}

3. BADGE COMPONENT
================================================================================

Purpose: Small status indicators
Uses: Level display, status markers, alert indicators

Variants:

Success (Green):
.badge-success {
    background: #4dff4d;
    color: #000;
}

Info (Cyan):
.badge-info {
    background: #4a9eff;
    color: #fff;
}

Warning (Orange):
.badge-warning {
    background: #ffaa00;
    color: #000;
}

Error (Red):
.badge-error {
    background: #ff4d4d;
    color: #fff;
}

CSS (Base):
.badge {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-lg {
    padding: 6px 14px;
    font-size: 12px;
}

4. BUTTON COMPONENT
================================================================================

Primary Button (Cyan):
<button class="btn btn-primary">Build</button>

CSS:
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 1px;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-primary {
    background: linear-gradient(135deg, #4a9eff, #2a7fff);
    color: #ffffff;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #5aa8ff, #3a8fff);
    box-shadow: 0 0 15px rgba(74, 158, 255, 0.5);
    transform: translateY(-2px);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: 0 0 8px rgba(74, 158, 255, 0.3);
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

Button Variants:

Secondary:
.btn-secondary {
    background: rgba(74, 158, 255, 0.2);
    color: #4a9eff;
    border: 1px solid #4a9eff;
}

Danger:
.btn-danger {
    background: linear-gradient(135deg, #ff4d4d, #cc0000);
    color: #ffffff;
}

Success:
.btn-success {
    background: linear-gradient(135deg, #4dff4d, #00cc00);
    color: #000;
}

Small:
.btn-sm {
    padding: 6px 12px;
    font-size: 11px;
}

Large:
.btn-lg {
    padding: 14px 28px;
    font-size: 14px;
}

Block:
.btn-block {
    width: 100%;
}

5. PROGRESS BAR COMPONENT
================================================================================

Purpose: Visual representation of progress/completion
Uses: Building countdown, research progress, resource storage

HTML:
<div class="progress">
    <div class="progress-bar progress-primary" style="width: 75%">
        <span class="progress-label">75%</span>
    </div>
</div>

CSS:
.progress {
    height: 24px;
    background: rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(74, 158, 255, 0.2);
    border-radius: 4px;
    overflow: hidden;
    position: relative;
    margin: 10px 0;
}

.progress-bar {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: width 0.3s ease, background 0.3s ease;
    position: relative;
    overflow: hidden;
}

.progress-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.progress-primary {
    background: linear-gradient(90deg, #4a9eff, #7ab8ff);
}

.progress-success {
    background: linear-gradient(90deg, #4dff4d, #66ff66);
}

.progress-warning {
    background: linear-gradient(90deg, #ffaa00, #ffcc00);
}

.progress-label {
    color: #fff;
    font-weight: bold;
    font-size: 12px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    z-index: 1;
}

6. INPUT & FORM ELEMENTS
================================================================================

Text Input:
<input type="text" class="form-control" placeholder="Enter value">

CSS:
.form-control {
    background: rgba(20, 20, 40, 0.8);
    color: #ffffff;
    border: 1px solid #4a9eff;
    border-radius: 5px;
    padding: 10px 12px;
    font-family: 'Courier New', monospace;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #7ab8ff;
    background: rgba(20, 20, 40, 0.95);
    box-shadow: 0 0 8px rgba(74, 158, 255, 0.3);
}

.form-control::placeholder {
    color: #666666;
}

Select/Dropdown:
<select class="form-control">
    <option>-- Select --</option>
    <option>Option 1</option>
</select>

CSS:
.form-control option {
    background: #0a0a1a;
    color: #ffffff;
}

Checkbox/Radio:
<label class="checkbox">
    <input type="checkbox">
    <span>Accept terms</span>
</label>

CSS:
.checkbox, .radio {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
}

.checkbox input, .radio input {
    accent-color: #4a9eff;
    cursor: pointer;
}

Form Group:
<div class="form-group">
    <label class="form-label">Building:</label>
    <input type="text" class="form-control" placeholder="Select building">
    <small class="form-text">Required field</small>
</div>

CSS:
.form-group {
    margin-bottom: 15px;
}

.form-label {
    display: block;
    margin-bottom: 5px;
    color: #4a9eff;
    font-weight: bold;
    font-size: 13px;
    text-transform: uppercase;
}

.form-text {
    display: block;
    margin-top: 5px;
    color: #aaaaaa;
    font-size: 12px;
}

.form-text.error {
    color: #ff4d4d;
}

7. MODAL / DIALOG COMPONENT
================================================================================

Purpose: Overlay small interactive windows
Uses: Confirmations, details, building info

HTML:
<div class="modal" id="buildModal">
    <div class="modal-backdrop"></div>
    <div class="modal-content">
        <div class="modal-header">
            <h2>Build Metal Mine</h2>
            <button class="modal-close">×</button>
        </div>
        <div class="modal-body">
            <div class="modal-info">
                <p>Level: 3 → 4</p>
                <p>Cost: Metal 1,920 Crystal 480</p>
                <p>Time: 1h 15m</p>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary">Cancel</button>
            <button class="btn btn-primary">Build Now</button>
        </div>
    </div>
</div>

CSS:
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2000;
}

.modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    animation: fadeIn 0.3s ease;
}

.modal-content {
    position: relative;
    z-index: 2001;
    background: linear-gradient(135deg, rgba(10, 10, 30, 0.95), rgba(20, 20, 50, 0.95));
    border: 2px solid #4a9eff;
    border-radius: 10px;
    max-width: 500px;
    width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.8);
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    background: rgba(74, 158, 255, 0.1);
    border-bottom: 2px solid #4a9eff;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    color: #4a9eff;
    margin: 0;
    font-size: 18px;
}

.modal-close {
    background: none;
    border: none;
    color: #4a9eff;
    font-size: 28px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.modal-close:hover {
    color: #7ab8ff;
    transform: scale(1.2);
}

.modal-body {
    padding: 20px;
    color: #ffffff;
    max-height: 400px;
    overflow-y: auto;
}

.modal-footer {
    background: rgba(0, 0, 0, 0.2);
    border-top: 1px solid rgba(74, 158, 255, 0.2);
    padding: 15px;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

8. TOOLTIP COMPONENT
================================================================================

Purpose: Additional information on hover/focus
Uses: Tech details, building bonuses, resource info

HTML:
<span class="tooltip" data-tooltip="Increases production by 10%">
    Mining Technology
</span>

CSS:
.tooltip {
    position: relative;
    cursor: help;
    border-bottom: 1px dotted #4a9eff;
}

.tooltip::after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0, 0, 0, 0.95);
    color: #fff;
    padding: 8px 12px;
    border-radius: 5px;
    font-size: 12px;
    border: 1px solid #4a9eff;
    white-space: nowrap;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.5);
}

.tooltip:hover::after {
    opacity: 1;
}

9. ALERT NOTIFICATION COMPONENT
================================================================================

Purpose: User notifications and feedback
Uses: Success messages, warnings, errors, info

HTML:
<div class="alert alert-success" role="alert">
    <span class="alert-icon">✓</span>
    <span class="alert-message">Building completed successfully!</span>
    <button class="alert-close">×</button>
</div>

CSS:
.alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 15px;
    border-radius: 5px;
    margin-bottom: 15px;
    border-left: 4px solid;
    animation: slideInRight 0.3s ease;
}

@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.alert-success {
    background: rgba(77, 255, 77, 0.1);
    border-left-color: #4dff4d;
    color: #4dff4d;
}

.alert-info {
    background: rgba(74, 158, 255, 0.1);
    border-left-color: #4a9eff;
    color: #4a9eff;
}

.alert-warning {
    background: rgba(255, 170, 0, 0.1);
    border-left-color: #ffaa00;
    color: #ffaa00;
}

.alert-error {
    background: rgba(255, 77, 77, 0.1);
    border-left-color: #ff4d4d;
    color: #ff4d4d;
}

.alert-icon {
    font-weight: bold;
    font-size: 16px;
}

.alert-message {
    flex: 1;
    font-size: 14px;
}

.alert-close {
    background: none;
    border: none;
    color: inherit;
    font-size: 18px;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s ease;
}

.alert-close:hover {
    opacity: 1;
}

10. TAB COMPONENT
================================================================================

Purpose: Organize content into tabbed sections
Uses: Building categories, tech tree sections, fleet types

HTML:
<div class="tabs">
    <ul class="tabs-nav">
        <li class="tab-item active">
            <a href="#tab-1" class="tab-link">Economy</a>
        </li>
        <li class="tab-item">
            <a href="#tab-2" class="tab-link">Warfare</a>
        </li>
        <li class="tab-item">
            <a href="#tab-3" class="tab-link">Fleet</a>
        </li>
    </ul>
    <div id="tab-1" class="tab-content active">
        <!-- Economy tech content -->
    </div>
    <div id="tab-2" class="tab-content">
        <!-- Warfare tech content -->
    </div>
    <div id="tab-3" class="tab-content">
        <!-- Fleet tech content -->
    </div>
</div>

CSS:
.tabs-nav {
    display: flex;
    list-style: none;
    border-bottom: 2px solid #4a9eff;
    gap: 0;
}

.tab-item {
    margin: 0;
}

.tab-link {
    display: block;
    color: #aaaaaa;
    padding: 12px 20px;
    text-decoration: none;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 1px;
}

.tab-link:hover {
    color: #4a9eff;
}

.tab-item.active .tab-link {
    color: #4a9eff;
    border-bottom-color: #4a9eff;
}

.tab-content {
    display: none;
    padding: 20px 0;
    animation: fadeIn 0.3s ease;
}

.tab-content.active {
    display: block;
}

================================================================================
SECTION 2: ADVANCED COMPONENTS
================================================================================

11. RESOURCE WIDGET
================================================================================

Purpose: Display current player resources
Location: Top navbar
Updates: Real-time via AJAX

HTML:
<div class="resource-display">
    <div class="resource-item">
        <span class="resource-icon">⛏️</span>
        <span class="resource-label">Metal</span>
        <span class="resource-value">50,342</span>
    </div>
    <div class="resource-item">
        <span class="resource-icon">💎</span>
        <span class="resource-label">Crystal</span>
        <span class="resource-value">30,218</span>
    </div>
    <div class="resource-item">
        <span class="resource-icon">🧪</span>
        <span class="resource-label">Deuterium</span>
        <span class="resource-value">5,042</span>
    </div>
    <div class="resource-item energy">
        <span class="resource-icon">⚡</span>
        <span class="resource-label">Energy</span>
        <span class="resource-value">342 / 500</span>
    </div>
</div>

CSS:
.resource-display {
    display: flex;
    gap: 15px;
    background: rgba(20, 20, 40, 0.9);
    padding: 10px 15px;
    border-radius: 8px;
    border: 1px solid rgba(74, 158, 255, 0.3);
}

.resource-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    min-width: 120px;
}

.resource-icon {
    font-size: 16px;
}

.resource-label {
    color: #4a9eff;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 45px;
}

.resource-value {
    color: #7ab8ff;
    font-weight: bold;
    font-family: 'Courier New', monospace;
}

.resource-item.energy .resource-value {
    color: #ffaa00;
}

12. BUILDING QUEUE WIDGET
================================================================================

Purpose: Display active construction/research
Html:
<div class="queue-widget">
    <h4 class="queue-title">Building Queue</h4>
    <div class="queue-item">
        <div class="queue-header">
            <span class="queue-name">Metal Mine Upgrade</span>
            <span class="queue-level">Lvl 4 → 5</span>
        </div>
        <div class="queue-progress">
            <div class="progress">
                <div class="progress-bar" style="width: 45%">
                    <span class="progress-label">45%</span>
                </div>
            </div>
        </div>
        <div class="queue-footer">
            <span class="queue-time">1h 15m remaining</span>
            <button class="btn-cancel">Cancel</button>
        </div>
    </div>
</div>

13. FLIGHT PATH/MOVEMENT WIDGET
================================================================================

Purpose: Track fleet movements
HTML:
<div class="flight-widget">
    <div class="flight-item active-flight">
        <div class="flight-header">
            <span class="flight-icon">🚀</span>
            <span class="flight-name">Strike Force</span>
            <span class="flight-status">In Transit</span>
        </div>
        <div class="flight-route">
            <span class="location-from">Earth</span>
            <span class="arrow">→</span>
            <span class="location-to">Mars</span>
        </div>
        <div class="flight-timer">
            <span class="timer-icon">⏱️</span>
            <span class="timer-value">30 minutes</span>
        </div>
    </div>
</div>

14. BUILDING ICON GRID
================================================================================

Purpose: Visual representation of buildings
Shows 4-6 buildings per row with icons

HTML:
<div class="building-grid">
    <div class="building-card">
        <div class="building-icon">🔨</div>
        <div class="building-name">Metal Mine</div>
        <div class="building-level">Level 5</div>
        <div class="building-prod">+1,200/hour</div>
    </div>
</div>

CSS:
.building-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
    gap: 12px;
}

.building-card {
    background: rgba(10, 10, 30, 0.8);
    border: 1px solid #4a9eff;
    border-radius: 8px;
    padding: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.building-card:hover {
    background: rgba(20, 20, 40, 0.9);
    box-shadow: 0 4px 12px rgba(74, 158, 255, 0.3);
    transform: translateY(-2px);
}

.building-icon {
    font-size: 32px;
    margin-bottom: 8px;
}

.building-name {
    font-size: 12px;
    color: #4a9eff;
    font-weight: bold;
    margin-bottom: 4px;
}

.building-level {
    font-size: 11px;
    color: #aaaaaa;
    margin-bottom: 4px;
}

.building-prod {
    font-size: 11px;
    color: #7ab8ff;
    font-weight: bold;
}

================================================================================
JAVASCRIPT UTILITIES
================================================================================

Key Functions (Index/js/components.js):

1. Modal Controller:
   - openModal(id)
   - closeModal(id)
   - toggleModal(id)

2. Tab Controller:
   - switchTab(tabId)
   - initTabs()

3. Resource Updater:
   - updateResources(data)
   - formatNumber(num)
   - animateResourceChange(oldVal, newVal)

4. Alert System:
   - showAlert(type, message, duration=5000)
   - closeAlert(elem)

5. Form Validation:
   - validateForm(formId)
   - checkRequired(field)
   - validateNumber(value, min, max)

6. Timer System:
   - startCountdown(elementId, seconds)
   - formatTime(seconds)
   - updateAllTimers()

================================================================================
ACCESSIBILITY CONSIDERATIONS
================================================================================

WCAG AA Compliance:
- Contrast ratios: 4.5:1 for normal text, 3:1 for large text
- All interactive elements keyboard accessible
- ARIA labels on complex components
- Focus indicators visible
- Color not only means of conveying information
- Forms have associated labels
- Error messages clear and actionable

Keyboard Navigation:
- Tab: Move to next element
- Shift+Tab: Previous element
- Enter: Activate button/link
- Escape: Close modal/dropdown
- Arrow keys: Navigate within menus

Screen Reader Support:
- role="..." attributes on custom components
- aria-label="" for icon-only buttons
- aria-current="page" on active nav items
- aria-expanded="" on collapsibles
- aria-hidden="true" on decorative elements

================================================================================
PERFORMANCE TIPS
================================================================================

CSS Optimization:
✅ Minimize box-shadow use (GPU intensive)
✅ Use transform/opacity for animations
✅ Avoid expensive layout triggers
✅ Mobile-first CSS media queries
✅ Minify production CSS

JavaScript Optimization:
✅ Debounce rapid DOM updates
✅ Use event delegation for lists
✅ Lazy-load modals/tabs
✅ Cache DOM selectors
✅ Batch DOM updates

Browser Support:
✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
✅ Mobile browsers (iOS Safari, Chrome Mobile)

Polyfills needed:
- CSS Grid (older browsers)
- Fetch API (IE11)
- Promise (IE11)

================================================================================
FILE STRUCTURE
================================================================================

Index/css/
├─ style.css (main styles - already created)
├─ components.css (component library - to create)
└─ responsive.css (media queries - optional)

Index/js/
├─ components.js (JS utilities - to create)
├─ handlers.js (event handlers - to create)
└─ utils.js (helper functions - to create)

================================================================================
NEXT STEPS
================================================================================

1. Create Index/css/components.css with all component styles
2. Create Index/js/components.js with utility functions
3. Update all page templates to use component classes
4. Create style guide documentation (HTML demo page)
5. Test accessibility compliance
6. Performance optimize delivered CSS/JS
7. Add TypeScript types (optional)
8. Create component documentation wiki

================================================================================
