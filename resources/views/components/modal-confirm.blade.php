<div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title" id="globalConfirmModalLabel">
                    <div class="d-flex align-items-center text-danger" id="globalConfirmHeader">
                        <i data-feather="alert-triangle" class="me-2"></i> Konfirmasi Aksi
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p id="globalConfirmMessage" class="mb-0 fs-5 text-dark">Apakah Anda yakin ingin melanjutkan aksi ini?</p>
                <p class="text-muted small mt-2 mb-0" id="globalConfirmSubtext">Tindakan ini mungkin tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light fw-medium" data-bs-dismiss="modal">Batal</button>
                <form id="globalConfirmForm" method="POST" action="" class="d-inline">
                    @csrf
                    <input type="hidden" name="_method" id="globalConfirmMethod" value="DELETE">
                    <button type="submit" class="btn btn-danger fw-medium px-4" id="globalConfirmBtn">Ya, Lanjutkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const confirmModal = document.getElementById('globalConfirmModal');
        if (confirmModal) {
            confirmModal.addEventListener('show.bs.modal', function (event) {
                // Button that triggered the modal
                const button = event.relatedTarget;
                
                // Extract info from data-bs-* attributes
                const actionUrl = button.getAttribute('data-bs-action');
                const method = button.getAttribute('data-bs-method') || 'DELETE';
                const message = button.getAttribute('data-bs-message') || 'Apakah Anda yakin ingin melanjutkan aksi ini?';
                const btnText = button.getAttribute('data-bs-btn-text') || 'Ya, Lanjutkan';
                const btnClass = button.getAttribute('data-bs-btn-class') || (method === 'DELETE' ? 'btn-danger' : 'btn-success');
                const showSubtext = button.getAttribute('data-bs-show-subtext') !== 'false';
                
                // Update the modal's content
                const form = document.getElementById('globalConfirmForm');
                const methodInput = document.getElementById('globalConfirmMethod');
                const messageEl = document.getElementById('globalConfirmMessage');
                const btnEl = document.getElementById('globalConfirmBtn');
                const headerEl = document.getElementById('globalConfirmHeader');
                const subtextEl = document.getElementById('globalConfirmSubtext');
                
                form.action = actionUrl;
                methodInput.value = method;
                messageEl.textContent = message;
                btnEl.textContent = btnText;
                
                // Reset classes
                btnEl.className = 'btn fw-medium px-4 ' + btnClass;
                
                // Header style
                if (method === 'DELETE') {
                    headerEl.className = 'd-flex align-items-center text-danger';
                    headerEl.innerHTML = '<i data-feather="alert-triangle" class="me-2"></i> Konfirmasi Hapus';
                } else {
                    headerEl.className = 'd-flex align-items-center text-success';
                    headerEl.innerHTML = '<i data-feather="help-circle" class="me-2"></i> Konfirmasi Aksi';
                }
                
                if (subtextEl) {
                    subtextEl.style.display = showSubtext ? 'block' : 'none';
                }
                
                // Re-init feather icons inside modal
                if (window.feather) {
                    window.feather.replace();
                }
            });
        }
    });
</script>
