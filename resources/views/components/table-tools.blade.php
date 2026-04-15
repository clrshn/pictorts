@php
    $toolbarId = 'tableTools_' . preg_replace('/[^A-Za-z0-9_]/', '_', $storageKey);
    $defaultHidden = $defaultHidden ?? [];
    $lockedColumns = $lockedColumns ?? [];
    $reportTitle = $reportTitle ?? 'Report';
@endphp

<div class="table-tools" id="{{ $toolbarId }}" style="display:flex; gap:10px; align-items:center; flex-wrap:wrap; overflow:visible;">
    <div style="position:relative; overflow:visible;">
        <button type="button" class="btn-gray" onclick="toggleTableToolsMenu_{{ $toolbarId }}()" style="min-width:120px; height:36px; display:inline-flex; align-items:center; justify-content:center;">
            <i class="fas fa-columns"></i> View / Hide
        </button>
        <div id="{{ $toolbarId }}_menu" style="display:none; position:absolute; top:42px; left:0; min-width:240px; max-width:min(320px, calc(100vw - 40px)); max-height:340px; overflow:auto; background:#fff; border:1px solid rgba(148,163,184,0.28); border-radius:12px; box-shadow:0 14px 30px rgba(15,23,42,0.18); padding:12px; z-index:2000;">
            <div style="font-weight:700; font-size:13px; color:#1f2937; margin-bottom:10px;">Visible Columns</div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                @foreach($columns as $key => $label)
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#334155;">
                        <input
                            type="checkbox"
                            data-column-key="{{ $key }}"
                            {{ in_array($key, $defaultHidden, true) ? '' : 'checked' }}
                            {{ in_array($key, $lockedColumns, true) ? 'disabled' : '' }}
                        >
                        <span>{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>

    <button type="button" class="btn-blue" onclick="openReportDialog_{{ $toolbarId }}()" style="min-width:150px; height:36px; display:inline-flex; align-items:center; justify-content:center;">
        <i class="fas fa-print"></i> Report Options
    </button>

    <a href="{{ $csvUrl }}" class="btn-green" style="min-width:130px; height:36px; display:inline-flex; align-items:center; justify-content:center;">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</div>

<div id="{{ $toolbarId }}_report_modal" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.45); z-index:9998; align-items:center; justify-content:center; padding:20px;">
    <div style="width:min(520px,100%); background:#fff; border-radius:18px; box-shadow:0 24px 60px rgba(15,23,42,0.2); overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid rgba(226,232,240,0.9); display:flex; align-items:center; justify-content:space-between;">
            <div style="font-size:16px; font-weight:700; color:#0f172a;">Generate Report</div>
            <button type="button" onclick="closeReportDialog_{{ $toolbarId }}()" style="border:none; background:none; color:#64748b; font-size:22px; cursor:pointer; line-height:1;">&times;</button>
        </div>
        <div style="padding:20px; display:grid; gap:16px;">
            <div>
                <label for="{{ $toolbarId }}_title" style="display:block; margin-bottom:6px; font-size:13px; font-weight:700; color:#334155;">Report Title</label>
                <input id="{{ $toolbarId }}_title" type="text" style="width:100%; height:40px; border:1px solid rgba(148,163,184,0.35); border-radius:10px; padding:0 12px;">
            </div>

            <div>
                <div style="margin-bottom:8px; font-size:13px; font-weight:700; color:#334155;">Report Scope</div>
                <div style="display:grid; gap:8px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#475569;">
                        <input type="radio" name="{{ $toolbarId }}_scope" value="filtered" checked>
                        <span>All filtered results</span>
                    </label>
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:#475569;">
                        <input type="radio" name="{{ $toolbarId }}_scope" value="selected">
                        <span>Only selected rows on this page</span>
                    </label>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px;">
                <div>
                    <label for="{{ $toolbarId }}_paper" style="display:block; margin-bottom:6px; font-size:13px; font-weight:700; color:#334155;">Paper Size</label>
                    <select id="{{ $toolbarId }}_paper" style="width:100%; height:40px; border:1px solid rgba(148,163,184,0.35); border-radius:10px; padding:0 12px;">
                        <option value="A4">A4</option>
                        <option value="Letter">Letter</option>
                        <option value="Legal">Legal</option>
                    </select>
                </div>
                <div>
                    <label for="{{ $toolbarId }}_orientation" style="display:block; margin-bottom:6px; font-size:13px; font-weight:700; color:#334155;">Orientation</label>
                    <select id="{{ $toolbarId }}_orientation" style="width:100%; height:40px; border:1px solid rgba(148,163,184,0.35); border-radius:10px; padding:0 12px;">
                        <option value="portrait">Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
            </div>

            <div style="font-size:12px; color:#64748b; line-height:1.5;">
                `All filtered results` uses the current page filters.
                `Only selected rows` uses the checkboxes and the columns you currently made visible.
            </div>

            <div style="display:flex; gap:10px; justify-content:flex-end; padding-top:4px;">
                <button type="button" class="btn-gray" onclick="closeReportDialog_{{ $toolbarId }}()" style="min-width:110px; height:38px; display:inline-flex; align-items:center; justify-content:center;">Cancel</button>
                <button type="button" class="btn-red" onclick="generateReport_{{ $toolbarId }}()" style="min-width:150px; height:38px; display:inline-flex; align-items:center; justify-content:center;">
                    <i class="fas fa-file-alt"></i> Generate Report
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const toolbarId = @json($toolbarId);
        const tableId = @json($tableId);
        const storageKey = @json($storageKey);
        const columns = @json(array_keys($columns));
        const columnLabels = @json($columns);
        const defaultHidden = @json($defaultHidden);
        const lockedColumns = @json($lockedColumns);
        const printUrl = @json($printUrl);
        const defaultTitle = @json($reportTitle);

        window[`toggleTableToolsMenu_${toolbarId}`] = function () {
            const menu = document.getElementById(`${toolbarId}_menu`);
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        };

        window[`openReportDialog_${toolbarId}`] = function () {
            const modal = document.getElementById(`${toolbarId}_report_modal`);
            const titleInput = document.getElementById(`${toolbarId}_title`);
            if (titleInput && !titleInput.value.trim()) {
                titleInput.value = defaultTitle;
            }
            modal.style.display = 'flex';
        };

        window[`closeReportDialog_${toolbarId}`] = function () {
            const modal = document.getElementById(`${toolbarId}_report_modal`);
            modal.style.display = 'none';
        };

        function applyVisibility(state) {
            const table = document.getElementById(tableId);
            if (!table) {
                return;
            }

            columns.forEach((key, index) => {
                const visible = state[key] !== false;
                table.querySelectorAll(`tr > *:nth-child(${index + 1})`).forEach((cell) => {
                    cell.style.display = visible ? '' : 'none';
                });
            });
        }

        function readCellText(cell) {
            const select = cell.querySelector('select');
            if (select) {
                return select.options[select.selectedIndex]?.text?.trim() || '';
            }

            return (cell.innerText || cell.textContent || '')
                .replace(/\s+/g, ' ')
                .trim();
        }

        function showMessage(message, title = 'Notice') {
            if (typeof window.showNotification === 'function') {
                window.showNotification({
                    type: 'warning',
                    title,
                    message,
                    duration: 3200,
                });
                return;
            }

            window.alert(message);
        }

        function buildPrintMarkup(title, headers, rows, options = {}) {
            const generatedAt = new Date().toLocaleString();
            const orientation = options.orientation || 'portrait';
            const paperSize = options.paperSize || 'A4';

            return `
                <!doctype html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <title>${title}</title>
                    <style>
                        @page { size: ${paperSize} ${orientation}; margin: 14mm; }
                        body { font-family: Arial, sans-serif; margin: 24px; color: #1f2937; }
                        h1 { margin: 0 0 8px; font-size: 24px; }
                        .meta { margin-bottom: 16px; color: #64748b; font-size: 13px; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #cbd5e1; padding: 10px 12px; font-size: 12px; text-align: left; vertical-align: top; }
                        th { background: #f8fafc; font-weight: 700; }
                    </style>
                </head>
                <body>
                    <h1>${title}</h1>
                    <div class="meta">
                        <div>Generated: ${generatedAt}</div>
                        <div>Paper: ${paperSize}</div>
                        <div>Orientation: ${orientation}</div>
                    </div>
                    <table>
                        <thead>
                            <tr>${headers.map((header) => `<th>${header}</th>`).join('')}</tr>
                        </thead>
                        <tbody>
                            ${rows.map((row) => `<tr>${row.map((value) => `<td>${String(value ?? '').replace(/</g, '&lt;').replace(/>/g, '&gt;')}</td>`).join('')}</tr>`).join('')}
                        </tbody>
                    </table>
                </body>
                </html>
            `;
        }

        function getReportOptions() {
            const title = document.getElementById(`${toolbarId}_title`)?.value?.trim() || defaultTitle;
            const paperSize = document.getElementById(`${toolbarId}_paper`)?.value || 'A4';
            const orientation = document.getElementById(`${toolbarId}_orientation`)?.value || 'portrait';
            const scope = document.querySelector(`input[name="${toolbarId}_scope"]:checked`)?.value || 'filtered';
            return { title, paperSize, orientation, scope };
        }

        function printSelectedRows(options = {}) {
            const table = document.getElementById(tableId);
            if (!table) {
                return;
            }

            const selectedRows = Array.from(table.querySelectorAll('tbody .table-row-select:checked'))
                .map((checkbox) => checkbox.closest('tr'))
                .filter(Boolean);

            if (!selectedRows.length) {
                showMessage('Select at least one row before generating a selected-row report.', 'Nothing Selected');
                return false;
            }

            const stored = localStorage.getItem(storageKey);
            let state = stored ? JSON.parse(stored) : {};
            lockedColumns.forEach((key) => {
                state[key] = true;
            });

            const printableKeys = columns.filter((key) => state[key] !== false && key !== 'row_select' && key !== 'action');
            if (!printableKeys.length) {
                showMessage('Show at least one printable column before generating the report.', 'No Printable Columns');
                return false;
            }

            const headers = printableKeys.map((key) => columnLabels[key] || key);
            const rows = selectedRows.map((row) => {
                return printableKeys.map((key) => {
                    const index = columns.indexOf(key) + 1;
                    const cell = row.querySelector(`td:nth-child(${index})`);
                    return cell ? readCellText(cell) : '';
                });
            });

            const printWindow = window.open('', '_blank', 'width=1200,height=800');
            if (!printWindow) {
                showMessage('Allow pop-ups to generate the selected-row report.', 'Popup Blocked');
                return false;
            }

            printWindow.document.open();
            printWindow.document.write(buildPrintMarkup(options.title || (document.title + ' - Selected Rows'), headers, rows, options));
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();
            return true;
        }

        window[`generateReport_${toolbarId}`] = function () {
            const options = getReportOptions();

            if (options.scope === 'selected') {
                if (printSelectedRows(options)) {
                    window[`closeReportDialog_${toolbarId}`]();
                }
                return;
            }

            const url = new URL(printUrl, window.location.origin);
            url.searchParams.set('report_title', options.title);
            url.searchParams.set('paper_size', options.paperSize);
            url.searchParams.set('orientation', options.orientation);
            const stored = localStorage.getItem(storageKey);
            let state = stored ? JSON.parse(stored) : {};
            lockedColumns.forEach((key) => {
                state[key] = true;
            });
            const visibleKeys = columns.filter((key) => state[key] !== false && key !== 'row_select' && key !== 'action');
            url.searchParams.set('visible_columns', visibleKeys.join(','));
            window.open(url.toString(), '_blank');
            window[`closeReportDialog_${toolbarId}`]();
        };

        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.getElementById(`${toolbarId}_menu`);
            const checkboxes = menu.querySelectorAll('input[type="checkbox"]');
            const table = document.getElementById(tableId);
            const modal = document.getElementById(`${toolbarId}_report_modal`);
            const titleInput = document.getElementById(`${toolbarId}_title`);

            if (titleInput && !titleInput.value.trim()) {
                titleInput.value = defaultTitle;
            }

            const stored = localStorage.getItem(storageKey);
            let state = {};

            if (stored) {
                state = JSON.parse(stored);
            } else {
                columns.forEach((key) => {
                    state[key] = !defaultHidden.includes(key);
                });
            }

            lockedColumns.forEach((key) => {
                state[key] = true;
            });

            checkboxes.forEach((checkbox) => {
                const key = checkbox.dataset.columnKey;
                checkbox.checked = state[key] !== false;

                checkbox.addEventListener('change', function () {
                    if (lockedColumns.includes(key)) {
                        checkbox.checked = true;
                        return;
                    }

                    state[key] = checkbox.checked;
                    localStorage.setItem(storageKey, JSON.stringify(state));
                    applyVisibility(state);
                });
            });

            document.addEventListener('click', function (event) {
                if (!event.target.closest(`#${toolbarId}`)) {
                    menu.style.display = 'none';
                }

                if (modal && event.target === modal) {
                    window[`closeReportDialog_${toolbarId}`]();
                }
            });

            applyVisibility(state);

            if (table) {
                const selectAll = table.querySelector('.table-select-all');
                const rowSelectors = table.querySelectorAll('.table-row-select');

                if (selectAll) {
                    selectAll.addEventListener('change', function () {
                        rowSelectors.forEach((checkbox) => {
                            checkbox.checked = selectAll.checked;
                        });
                    });
                }

                rowSelectors.forEach((checkbox) => {
                    checkbox.addEventListener('change', function () {
                        if (!selectAll) {
                            return;
                        }

                        const checkedCount = Array.from(rowSelectors).filter((item) => item.checked).length;
                        selectAll.checked = checkedCount === rowSelectors.length && rowSelectors.length > 0;
                        selectAll.indeterminate = checkedCount > 0 && checkedCount < rowSelectors.length;
                    });
                });
            }
        });
    })();
</script>
