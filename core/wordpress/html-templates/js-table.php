                $("#mwpc-canvas-%id:last").after(`
                <table style="padding:5px;" id="mwpc-item-%id-` + mwpc_counter_%id + `" class="presentation">
                    <tbody id="mwpc-detail-body-%id">
                        <tr>
                            %jsfields
                        </tr>
                        <tr>
                            <td>
                                <div id="mwpc-save-%id" itemid="` + mwpc_counter_%id + `" class="button action">
                                    Salvar
                                </div>
                            </td>
                            <td>
                                <div itemid="` + mwpc_counter_%id + `" id="mwpc-cancel-%id" class="button action">
                                    Cancelar
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                `);
