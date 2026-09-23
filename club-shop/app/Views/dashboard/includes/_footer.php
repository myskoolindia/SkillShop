</section>
</div>
</div>
<?= aiWriter()->status == 1 && hasPermission('ai_writer') ? view('admin/includes/_ai_writer', ['aiContentType' => 'product']) : ''; ?>
<script src="<?= base_url('assets/admin/js/jquery-ui.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/datatables/dataTables.bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/adminlte.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/pace/pace.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/plugins-2.6.js'); ?>"></script>
<script src="<?= base_url('assets/admin/vendor/magnific-popup/jquery.magnific-popup.min.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/admin-2.6.js'); ?>"></script>
<script src="<?= base_url('assets/admin/js/dashboard-2.6.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/file-manager/file-manager.js'); ?>"></script>
<script>$('<input>').attr({type: 'hidden', name: 'back_url', value: '<?= getCurrentUrl(); ?>'}).appendTo('form[method="post"]');</script>
<script>$('<input>').attr({type: 'hidden', name: 'sysLangId', value: '<?=selectedLangId(); ?>'}).appendTo('form[method="post"]');</script>
<script>
    $(document).ready(function () {
        $('.dataTable').DataTable({
            "order": [[0, "desc"]],
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "<?= trans('show'); ?> _MENU_",
                "search": "<?= trans('search'); ?>:",
                "zeroRecords": "<?= trans('no_records_found'); ?>"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "<?= trans('number_of_entries'); ?>: " + total : '';
            }
        });
        $('.dataTableNoSort').DataTable({
            "ordering": false,
            "aLengthMenu": [[15, 30, 60, 100], [15, 30, 60, 100, "All"]],
            "language": {
                "lengthMenu": "<?= trans('show'); ?> _MENU_",
                "search": "<?= trans('search'); ?>:",
                "zeroRecords": "<?= trans('no_records_found'); ?>"
            },
            "infoCallback": function (settings, start, end, max, total, pre) {
                return total > 0 ? "<?= trans('number_of_entries'); ?>: " + total : '';
            }
        });
    });

    function initTinyMCE(selector, minHeight) {
        var menuBar = 'file insert format table help';
        if (selector == '.tinyMCEsmall') {
            menuBar = false;
        }
        tinymce.init({
            selector: selector,
            height: minHeight,
            min_height: minHeight,
            valid_elements: '*[*]',
            entity_encoding: 'raw',
            relative_urls: false,
            remove_script_host: false,
            directionality: MdsConfig.directionality,
            language: '<?= $activeLang->text_editor_lang; ?>',
            menubar: menuBar,
            plugins: 'advlist autolink lists link image charmap preview searchreplace visualblocks code codesample fullscreen insertdatetime media table',
            toolbar: 'fullscreen code preview | undo redo | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | numlist bullist | forecolor backcolor removeformat | image media link',
            content_css: ['<?= base_url('assets/vendor/tinymce/editor_content.css'); ?>'],
            mobile: {
                menubar: menuBar
            }
        });
    }
    if ($('.tinyMCE').length > 0) {
        initTinyMCE('.tinyMCE', 400);
    }
    if ($('.tinyMCEsmall').length > 0) {
        initTinyMCE('.tinyMCEsmall', 300);
    }
</script>
<div class="modal fade" id="addressModal" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Shipping Address</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body" id="modalBody"></div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button class="btn btn-primary" onclick="printAddress()">Print</button>
      </div>

    </div>
  </div>
</div>

<script>
const modal = document.getElementById('addressModal');

modal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const data = JSON.parse(button.getAttribute('data-order'));

    let ship = data.shipping;
    if (typeof ship === 'string') {
        ship = JSON.parse(ship);
    }

    document.getElementById('modalBody').innerHTML = `
        <strong>Order:</strong> ${data.order_number}<br><br>
        <strong>${ship.sFirstName} ${ship.sLastName}</strong><br>
        ${ship.sAddress}<br>
        ${ship.sCity}, ${ship.sState} - ${ship.sZipCode}<br>
        ${ship.sCountry}<br>
        📞 ${ship.sPhoneNumber}
    `;
});

function printAddress(){
    const w = window.open('', '', 'width=400,height=600');
    w.document.write(`
        <html><head><title>Print</title></head>
        <body>${document.getElementById('modalBody').innerHTML}</body>
        </html>
    `);
    w.print();
    w.close();
}
</script>
</body>
</html>