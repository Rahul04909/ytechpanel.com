<!-- ==========================================
     POPUP MODAL FORMS
     Included from header.php — all 4 enquiry modals
     ========================================== -->
<style>
/* ── Modal Overlay ── */
.pop-modal-overlay {
    display: none;
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.55);
    z-index: 99999;
    align-items: center; justify-content: center;
    padding: 20px;
    overflow-y: auto;
}
.pop-modal-overlay.active { display: flex; }

.pop-modal {
    background: #fff;
    width: 100%; max-width: 520px;
    position: relative;
    box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    animation: popFadeIn 0.25s ease;
}
@keyframes popFadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.pop-modal-close {
    position: absolute; top: 12px; right: 12px;
    width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    background: #f1f5f9; border: none; cursor: pointer;
    transition: all 0.2s; z-index: 2;
}
.pop-modal-close:hover { background: #eff6ff; color: #0b4a83; }
.pop-modal-close svg { width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2.5; stroke-linecap: round; }

.pop-modal-header {
    padding: 20px 24px 14px;
    border-bottom: 1px solid #e2e8f0;
}
.pop-modal-header h3 {
    font-family: 'Outfit', sans-serif;
    font-size: 18px; font-weight: 700; color: #1e293b; margin: 0;
    display: flex; align-items: center; gap: 8px;
}
.pop-modal-header h3 svg { width: 20px; height: 20px; fill: none; stroke: #0b4a83; stroke-width: 2; stroke-linecap: round; }
.pop-modal-header p {
    font-size: 13px; color: #64748b; margin: 4px 0 0;
}

.pop-modal-body { padding: 20px 24px; }

.pop-field { margin-bottom: 14px; }
.pop-label {
    display: block; font-size: 12px; font-weight: 600; color: #334155; margin-bottom: 4px;
}
.pop-label .req { color: #f26522; }
.pop-input {
    width: 100%; padding: 9px 12px;
    border: 1.5px solid #e2e8f0; border-radius: 4px;
    font-size: 13px; font-family: inherit; color: #1e293b;
    box-sizing: border-box; transition: all 0.2s;
}
.pop-input:focus { outline: none; border-color: #0b4a83; box-shadow: 0 0 0 3px rgba(11,74,131,0.06); }
.pop-textarea { resize: vertical; min-height: 64px; }
.pop-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

.pop-submit {
    width: 100%; padding: 10px 20px;
    background: #0b4a83; color: #fff; border: none;
    font-size: 14px; font-weight: 600; font-family: inherit;
    cursor: pointer; transition: all 0.2s;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.pop-submit:hover { background: #07355f; box-shadow: 0 4px 12px rgba(11,74,131,0.25); }
.pop-submit:disabled { background: #93c5fd; cursor: not-allowed; box-shadow: none; }
.pop-submit svg { width: 16px; height: 16px; fill: none; stroke: #fff; stroke-width: 2.5; stroke-linecap: round; }
@keyframes popSpin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

.pop-success {
    display: none; text-align: center; padding: 30px 20px;
}
.pop-success.active { display: block; }
.pop-success svg { width: 48px; height: 48px; fill: none; stroke: #16a34a; stroke-width: 2; stroke-linecap: round; }
.pop-success h4 { font-family: 'Outfit', sans-serif; font-size: 18px; color: #1e293b; margin: 12px 0 4px; }
.pop-success p { font-size: 13px; color: #64748b; margin: 0; }

@media (max-width: 576px) {
    .pop-modal { max-width: 100%; }
    .pop-row { grid-template-columns: 1fr; }
    .pop-modal-header { padding: 16px 18px 12px; }
    .pop-modal-header h3 { font-size: 16px; }
    .pop-modal-body { padding: 16px 18px; }
}
</style>

<script>
function openPopModal(id) { document.getElementById(id).classList.add('active'); document.body.style.overflow = 'hidden'; }
function closePopModal(id) { document.getElementById(id).classList.remove('active'); document.body.style.overflow = ''; }
function closePopOnOverlay(el, e) { if (e.target === el) el.classList.remove('active'); document.body.style.overflow = ''; }

function submitPopForm(formId, btnId, successId) {
    const form = document.getElementById(formId);
    const btn = document.getElementById(btnId);
    const success = document.getElementById(successId);

    // Store original button HTML for restoration on failure
    if (!btn.dataset.origHtml) btn.dataset.origHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg viewBox="0 0 24 24" style="animation:popSpin 1s linear infinite;width:16px;height:16px;"><path d="M12 2v4"/></svg> Submitting...';

    fetch('handlers/frontend-handler.php', {
        method: 'POST',
        body: new URLSearchParams(new FormData(form))
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            form.style.display = 'none';
            success.querySelector('p').textContent = data.message;
            success.classList.add('active');
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = btn.dataset.origHtml;
        }
    })
    .catch(() => {
        alert('Network error. Please try again.');
        btn.disabled = false;
    });
}
</script>

<!-- ====== MODAL 1: GET A QUOTE / CUSTOM QUOTE ====== -->
<div class="pop-modal-overlay" id="modalQuote" onclick="closePopOnOverlay(this, event)">
    <div class="pop-modal">
        <button class="pop-modal-close" onclick="closePopModal('modalQuote')" aria-label="Close">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="pop-modal-header">
            <h3><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg> Get A Quote</h3>
            <p>Fill in your details and our team will respond with the best pricing for your requirements.</p>
        </div>
        <div class="pop-modal-body">
            <form id="popQuoteForm" onsubmit="event.preventDefault(); submitPopForm('popQuoteForm','popQuoteBtn','popQuoteSuccess')">
                <input type="hidden" name="action" value="submit_quote">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Your Name <span class="req">*</span></label><input type="text" class="pop-input" name="name" required></div>
                    <div class="pop-field"><label class="pop-label">Email <span class="req">*</span></label><input type="email" class="pop-input" name="email" required></div>
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Phone</label><input type="tel" class="pop-input" name="phone" placeholder="+91-XXXXXXXXXX"></div>
                    <div class="pop-field"><label class="pop-label">Company</label><input type="text" class="pop-input" name="company"></div>
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Product Interest</label>
                        <select class="pop-input" name="product_interest">
                            <option value="">Select product</option>
                            <option>PCC Panel</option>
                            <option>MCC Panel</option>
                            <option>APFC Panel</option>
                            <option>PLC Panel</option>
                            <option>Distribution Board</option>
                            <option>HT Panel</option>
                            <option>LT Panel</option>
                            <option>Busbar Trunking</option>
                            <option>Control Desk</option>
                            <option>Other</option>
                        </select>
                    </div>
                    <div class="pop-field"><label class="pop-label">Quantity</label><input type="text" class="pop-input" name="quantity" placeholder="e.g. 2 units"></div>
                </div>
                <div class="pop-field"><label class="pop-label">Message / Requirements <span class="req">*</span></label><textarea class="pop-input pop-textarea" name="message" required></textarea></div>
                <button type="submit" class="pop-submit" id="popQuoteBtn">
                    <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Submit Quote Request
                </button>
            </form>
            <div class="pop-success" id="popQuoteSuccess">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <h4>Quote Request Submitted!</h4>
                <p></p>
            </div>
        </div>
    </div>
</div>

<!-- ====== MODAL 2: SUBMIT ENQUIRY ====== -->
<div class="pop-modal-overlay" id="modalEnquiry" onclick="closePopOnOverlay(this, event)">
    <div class="pop-modal">
        <button class="pop-modal-close" onclick="closePopModal('modalEnquiry')" aria-label="Close">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="pop-modal-header">
            <h3><svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg> Submit Enquiry</h3>
            <p>Have a question or need more information? Send us your enquiry and we'll get back to you.</p>
        </div>
        <div class="pop-modal-body">
            <form id="popEnquiryForm" onsubmit="event.preventDefault(); submitPopForm('popEnquiryForm','popEnquiryBtn','popEnquirySuccess')">
                <input type="hidden" name="action" value="submit_enquiry_popup">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Your Name <span class="req">*</span></label><input type="text" class="pop-input" name="name" required></div>
                    <div class="pop-field"><label class="pop-label">Email <span class="req">*</span></label><input type="email" class="pop-input" name="email" required></div>
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Phone</label><input type="tel" class="pop-input" name="phone" placeholder="+91-XXXXXXXXXX"></div>
                    <div class="pop-field"><label class="pop-label">Subject</label><input type="text" class="pop-input" name="subject" placeholder="e.g. Product inquiry"></div>
                </div>
                <div class="pop-field"><label class="pop-label">Message <span class="req">*</span></label><textarea class="pop-input pop-textarea" name="message" required></textarea></div>
                <button type="submit" class="pop-submit" id="popEnquiryBtn">
                    <svg viewBox="0 0 24 24"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    Send Enquiry
                </button>
            </form>
            <div class="pop-success" id="popEnquirySuccess">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <h4>Enquiry Submitted!</h4>
                <p></p>
            </div>
        </div>
    </div>
</div>

<!-- ====== MODAL 3: REQUEST CALL BACK ====== -->
<div class="pop-modal-overlay" id="modalCallback" onclick="closePopOnOverlay(this, event)">
    <div class="pop-modal">
        <button class="pop-modal-close" onclick="closePopModal('modalCallback')" aria-label="Close">
            <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <div class="pop-modal-header">
            <h3><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg> Request Call Back</h3>
            <p>Leave your phone number and we'll call you back at your preferred time.</p>
        </div>
        <div class="pop-modal-body">
            <form id="popCallbackForm" onsubmit="event.preventDefault(); submitPopForm('popCallbackForm','popCallbackBtn','popCallbackSuccess')">
                <input type="hidden" name="action" value="submit_callback">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <div style="position:absolute;left:-9999px;opacity:0;height:0;overflow:hidden;">
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Your Name <span class="req">*</span></label><input type="text" class="pop-input" name="name" required></div>
                    <div class="pop-field"><label class="pop-label">Phone Number <span class="req">*</span></label><input type="tel" class="pop-input" name="phone" required placeholder="+91-XXXXXXXXXX"></div>
                </div>
                <div class="pop-row">
                    <div class="pop-field"><label class="pop-label">Email</label><input type="email" class="pop-input" name="email"></div>
                    <div class="pop-field"><label class="pop-label">Preferred Time</label>
                        <select class="pop-input" name="preferred_time">
                            <option value="">Anytime</option>
                            <option>Morning (9AM–12PM)</option>
                            <option>Afternoon (12PM–3PM)</option>
                            <option>Evening (3PM–6PM)</option>
                        </select>
                    </div>
                </div>
                <div class="pop-field"><label class="pop-label">Message (optional)</label><textarea class="pop-input pop-textarea" name="message" placeholder="Brief description of what you'd like to discuss..."></textarea></div>
                <button type="submit" class="pop-submit" id="popCallbackBtn">
                    <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.21h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.91a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    Request Call Back
                </button>
            </form>
            <div class="pop-success" id="popCallbackSuccess">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <h4>Callback Requested!</h4>
                <p></p>
            </div>
        </div>
    </div>
</div>
