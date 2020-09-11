<td scope="row">
    <div id="mwpc-delete-%itemid" itemid="%itemid" class="button action">
        %label
    </div>
</td>

<script>
    jQuery(document).ready(function ($) {
        $("#mwpc-delete-%itemid").click(function() {
            console.log(this.getAttribute('itemid'));
            $('#mwpc-detail-row-%id-' + this.getAttribute("itemid")).remove();
        });
    });
</script>