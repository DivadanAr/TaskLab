<div class="modal-overlay" id="boardModal">

    <div class="modal">

        <div class="modal-header">
            <div>
                <h2>Create New Board</h2>
                <p>Create a new board to organize your tasks.</p>
            </div>

            <button class="modal-close" onclick="closeBoardModal()">
                <i class="fa-solid fa-x" style="font-size: 15px;"></i>
            </button>
        </div>

        <form action="./actions/board/board_post.php" method="POST" enctype="multipart/form-data">

            <div class="form-group" style="padding:10px 24px; margin-top: 10px;">

                <label>Board Name <span style="color: red;">*</span></label>

                <input type="text" name="board_name" id="board_name" placeholder="Contoh: Pekerjaan Rumah"
                    required>

            </div>

            <div class="form-group" style="padding:0 24px 10px;">

                <label for="board_description">Deskripsi</label>

                <textarea name="description" id="board_description" rows="5"
                    placeholder="Ceritain board ini buat apa…"></textarea>

            </div>

            <div class="form-group board-form-group">
                <label for="cover_board">Cover Board</label>

                <div class="container-board-input">
                    <div class="board-file-upload">
                        <input
                            type="file"
                            id="cover_board"
                            name="cover_board"
                            accept="image/*"
                            hidden>

                        <label for="cover_board" class="board-file-button">
                            <i class="fas fa-image"></i>
                            <span style="margin-left: 5px; color: var(--text-hi);">Pilih Gambar</span>
                        </label>

                        <span class="board-file-name">Belum ada gambar dipilih</span>
                    </div>
                </div>

                <small class="board-file-hint">
                    JPG, PNG, WEBP, GIF • Maks. 5 MB
                </small>
            </div>

            <div class="modal-footer">

                <button type="button" class="btn-cancel" onclick="closeBoardModal()">
                    Cancel
                </button>

                <button type="submit" class="btn-save">
                    Create Board
                </button>

            </div>

        </form>

    </div>

</div>

<style>
    /* Penyelaras tampilan CKEditor supaya nyambung sama desain app.
       Ditulis inline (bukan file terpisah) karena partial ini di-include
       dari root (dashboard.php) & dari subfolder (dashboard/*.php) sekaligus,
       jadi gak ada satu path relatif yang aman buat dua-duanya. */

    .ck.ck-editor {
        --ck-border-radius: var(--r-md);
    }

    .ck.ck-editor__main>.ck-editor__editable,
    .ck-blurred {
        min-height: 140px;
        background: var(--bg-deep);
        color: var(--text-hi);
    }

    .ck-reset_all :not(.ck-reset_all-excluded *),
    .ck.ck-reset_all {

        color: var(--text-hi);
    }

    .ck.ck-toolbar {
        background: var(--bg-card) !important;
        border-color: var(--bg-card) !important;
    }

    .ck.ck-editor__main>.ck-editor__editable.ck-focused {
        border-color: var(--bg-deep) !important;
        box-shadow: 0 0 0 3px var(--accent-dim) !important;
    }

    .ck.ck-button:not(.ck-disabled):hover,
    .ck.ck-button:not(.ck-disabled).ck-on {
        background: var(--accent-dim) !important;
        color: var(--accent) !important;
    }
</style>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
    const coverInput = document.getElementById("cover_board");
    const coverName = document.querySelector(".board-file-name");

    coverInput.addEventListener("change", function() {
        if (this.files.length) {
            coverName.textContent = this.files[0].name;
        } else {
            coverName.textContent = "Belum ada gambar dipilih";
        }
    });

    // ---------- CKEditor buat field Deskripsi ----------
    let boardDescEditor = null;

    ClassicEditor
        .create(document.querySelector('#board_description'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'link', 'blockQuote', '|',
                'undo', 'redo'
            ]
        })
        .then((editor) => {
            boardDescEditor = editor;
        })
        .catch((error) => {
            console.error('Gagal memuat editor deskripsi:', error);
        });

    // Sinkronkan isi editor ke textarea aslinya sebelum form dikirim,
    // karena CKEditor gak otomatis nulis balik ke <textarea> yang dia gantikan.
    document.querySelector('#boardModal form').addEventListener('submit', function() {
        if (boardDescEditor) {
            document.querySelector('#board_description').value = boardDescEditor.getData();
        }
    });

    function openBoardModal() {
        document
            .getElementById("boardModal")
            .classList.add("show");

        document
            .getElementById("board_name")
            .focus();
    }

    function closeBoardModal() {
        document
            .getElementById("boardModal")
            .classList.remove("show");
    }

    document
        .getElementById("boardModal")
        .addEventListener("click", function(e) {

            if (e.target === this) {

                closeBoardModal();

            }

        });
</script>