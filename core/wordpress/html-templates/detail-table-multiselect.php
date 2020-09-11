<style>
#table-mwpc-wrapper-%id {
  position:relative;
}
#table-mwpc-scroll-%id {
  height:250px;
  overflow:auto;  
  margin-top:20px;
}
#table-mwpc-wrapper-%id table {
  width:100%;
    
}
</style>
<!--
<p style="padding:10px;">
    <label>%searchlabel</label>
    <input class="regular-text" id="mwpc-filter" type="text" placeholder="%searchplaceholder">
</p>
-->
<div id="table-mwpc-wrapper-%id">
  <div id="table-mwpc-scroll-%id">
    <table class="wp-list-table widefat striped table-view-list">
        <thead>
            <tr>
                <th style="width:5%" class="manage-column check-column" scope="col"></th>
                %columns
            </tr>
        </thead>
        <tbody id="mwpc-table-%id">
            <tr></tr>
            %rows             
        </tbody>
    </table>
  </div>
</div>