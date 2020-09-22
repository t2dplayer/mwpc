<td scope="row">
    <div id="mwpc-delete-%itemid" itemid="%itemid" class="button action">
        %label
    </div>
</td>

<script>
    jQuery(document).ready(function ($) {
        $("#mwpc-delete-%itemid").click(function() {
            $("form#mwpc-form div#mwpc-delete-commands div:last").after('<div><input type="hidden" name="delete_%id[]" value="' + this.getAttribute("itemid") + '"></div>');
            $('#mwpc-detail-row-%id-' + this.getAttribute("itemid")).remove();
        });
    });
</script>