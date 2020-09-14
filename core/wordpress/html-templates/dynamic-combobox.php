<!--
    '%id'=>,
    '%ifelse'=>,
    '%tablevalues'=>,    
!-->

<script>
    jQuery(document).ready(function ($) {
        $("#mwpc-plus-%id").click(function() {
            if (typeof mwpc_counter_%id == 'undefined') {
                mwpc_counter_%id = 0;
            }
            mwpc_counter_%id++;
            let value = $("#mwpc-type-%id").children("option:selected").val();
            %ifelse
            $("#mwpc-cancel-%id").click(function() {
                $('#mwpc-item-%id-' + this.getAttribute("itemid")).remove();
            });
            $("#mwpc-save-%id").click(function(){
                let validated = true;
                let objects = $('tbody#mwpc-detail-body-%id tr td input');
                for (i=0; i<objects.length;++i) {
                    if (objects[i].required && objects[i].value.length == 0) {
                        objects[i].setAttribute('placeholder', 'Preencha este campo');
                        objects[i].focus();
                        validated = false;
                        break;
                    }
                }
                if (validated) {
                    let itemid = this.getAttribute("itemid");
                    let row = '<tr id="mwpc-detail-unsaved-row-%id-' + itemid + '">';
                    key = "";
                    for (i=0; i<objects.length;++i) {
                        key += objects[i].id + ':' + objects[i].value;
                        if (i < objects.length - 1) key += ";";
                    }
                    let select_objects = $('tbody#mwpc-detail-body-%id tr td select');
                    for (i=0; i<select_objects.length;++i) {
                        if (objects.length > 0 && i === 0) key += ";";
                        key += select_objects[i].id + ':' + select_objects[i].value;
                        if (i < select_objects.length - 1) key += ";";
                    }                    
                    row += '<td></td><td><input type="hidden" name="%id[]" value="' + key + '"></td>';
                    for (i=0; i<objects.length;++i) {                            
                        row += '<td>' + objects[i].value + '</td>';
                    }
                    for (i=0; i<select_objects.length;++i) {
                        row += '<td>' + select_objects[i].value + '</td>';
                    }                     
                    row += '<td><div id="mwpc-delete-' + itemid + '" itemid="' + itemid + '" class="button action">Apagar</div></td>';
                    row += "</tr>";
                    str_delete = `
                    <script>
                    jQuery(document).ready(function ($) {
                        $("#mwpc-delete-` + itemid + `").click(function() {
                            console.log(this.getAttribute('itemid'));
                            $("#mwpc-detail-unsaved-row-%id-` + this.getAttribute("itemid") + `").remove();
                        });
                    });
                    <\/script>
                    `;
                    $("#mwpc-table-%id tr:last").after(row);
                    $("#mwpc-table-%id").after(str_delete);
                    $('#mwpc-item-%id-' + this.getAttribute("itemid")).remove();
                }
            });             
        });
    });
</script>

<div class="mwpc-combobox-%id" style="float: inline">
    <select id="mwpc-type-%id">
        %options
    </select>      
    <div id="mwpc-plus-%id" class="button action">+</div>
</div>
<div id="mwpc-canvas-%id">
</div>