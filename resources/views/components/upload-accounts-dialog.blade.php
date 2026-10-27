<div class="modal modal-lg fade" id="uploadAccountsDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="uploadAccountsDialogLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #283618;">
                <h1 class="modal-title fs-5 fw-bold" id="uploadAccountsDialogLabel" style="color: #FEFAE0;">Import Akun
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                    style="filter: brightness(0) saturate(100%) invert(91%) sepia(2%) saturate(2077%) hue-rotate(355deg) brightness(108%) contrast(99%);"></button>
            </div>
            <div class="modal-body" style="background-color: #606C38;">
                <p class="fw-bold" style="color: #FEFAE0;">
                    Masukkan file .xlsx (<a
                        href="{{ asset('Template Daftar Akun Web Logbook Dokter Magang.xlsx') }}">template</a>)
                </p>
                <form method="POST" action="{{ route('akun.import') }}" enctype="multipart/form-data" class="dropzone"
                    style="background-color: #FEFAE0;" id="file-upload-input">
                    @csrf
                </form>
            </div>
            <div class="modal-footer" style="background-color: #606C38;">
                <button type="button" class="btn btn-primary" id="file-upload-input-submit-button">Upload</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        Dropzone.options.fileUploadInput = { // camelized version of the `id`
            paramName: "accounts_xlsx",
            autoProcessQueue: false,
            maxFilesize: 2,
            acceptedFiles: '.xlsx',
            dictDefaultMessage: `
                <div class="d-flex flex-column gap-4">
                    <i class="bi bi-inbox text-primary" style="font-size: 48px;"></i>

                    <div>
                        <p>Klik atau drag file ke area ini untuk mengupload</p>
                        <p class="text-secondary">Maximum file size 2MB</p>
                    </div>
                </div>
            `,
            init: function() {
                const myDropzone = this;

                document.querySelector('#file-upload-input-submit-button').addEventListener('click', function() {
                    myDropzone.processQueue();
                })

                this.on("success", function(file) {
                    const alert = new DOMParser()
                        .parseFromString(file.xhr.responseText, 'text/html')
                        .querySelector('.alert');

                    if (alert) {
                        sessionStorage.setItem('importAlert', alert.outerHTML);
                    }
                });

                this.on("queuecomplete", function() {
                    this.removeAllFiles();
                    window.location.reload();
                });
            },
        }

        const importAlert = sessionStorage.getItem('importAlert');
        if (importAlert) {
            sessionStorage.removeItem('importAlert');
            document.querySelector('#importAlertSlot').insertAdjacentHTML('beforeend', importAlert);
        }
    </script>
@endpush
