<style>
#table-mwpc-wrapper {
  position:relative;
}
#table-mwpc-scroll {
  height:150px;
  overflow:auto;  
  margin-top:20px;
}
#table-mwpc-wrapper table {
  width:100%;
    
}
</style>
<!--
<p style="padding:10px;">
    <label>%searchlabel</label>
    <input class="regular-text" id="mwpc-filter" type="text" placeholder="%searchplaceholder">
</p>
-->
<div id="table-mwpc-wrapper">
  <div id="table-mwpc-scroll">
    <table class="wp-list-table widefat striped table-view-list">
        <thead>
            <tr>
                <th style="width:5%" class="manage-column check-column" scope="col"></th>
                %columns
            </tr>
        </thead>
        <tbody id="mwpc-table">        
            %rows
        </tbody>
    </table>
  </div>
</div>