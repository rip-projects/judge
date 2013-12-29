<script type="text/javascript">
					function showhide(){
					//	alert($('select[name=role_id]').val());
						if( $('select[name=level_id]').val()==2||$('select[name=level_id]').val()==1){
							$("#mng").show();
							$("#lead").show();
						}else{
							$("#mng").hide();
							$("#lead").hide();
						}
					}

					function countAverage(){
						var total=0;
						var counter=0;
						
						$.each($('input:radio'), function(index, value) { 
							if(value.checked){
								total+=parseInt(value.value);
								counter++;
							}	  
						});
						$("#average").val((total/counter).toFixed(2).replace(/\.?0+$/, ""));
			
					}	
					
                	$(function() {
                    	showhide();
                    	$('select[name=level_id]').change(showhide);
                    	$('input:radio').change(countAverage);
                        var data = "<?php echo site_url('rpc/user_options') ?>";
                        var options = {
                            dataType: 'json',
                            minChars: 0,
                            max: 15,
                            autoFill: false,
                            mustMatch: false,
                            matchContains: false,
                            selectFirst: false,
                            scrollHeight: 220,
                            parse: function(data) {
                                var result = [];
                                for(var i = 0; i < data.length; i++) {
                                    if (typeof(data[i]['value']) == 'undefined') {
                                        result.push({
                                            'data': data[i],
                                            'result': data[i]['key'],
                                            'value': data[i]['key']
                                        });
                                    } else {
                                        result.push({
                                            'data': data[i],
                                            'result': data[i]['value'],
                                            'value': data[i]['key']
                                        });
                                    }
                                                                                        
                                }
                                return result;
                                                                                                        
                            },
                            formatItem: function(row) {
                                var result = [];
                                for(var key in row) {
                                    result.push(row[key]);
                                }
                                return result.join(' - ');
                            },
                            formatResult: function(row) {
                                return row['key'];
                            }
                        };
                        $('input[name=user]').autocomplete(data, options);
                        $('input[name=user]').result(function(evt, obj, result) {
                            if (typeof(result) == 'undefined') {
                                result = '';
                            }
                            $('input[name=user_id]').val(result);
                        });
                        $('input[name=manager]').autocomplete(data, options);
                        $('input[name=manager]').result(function(evt, obj, result) {
                            if (typeof(result) == 'undefined') {
                                result = '';
                            }
                            $('input[name=manager_id]').val(result);
                        });	
							                        
                    });
                </script>

<form action="" method="post" class="ajaxform">
    <fieldset>
        <legend> Detail Karyawan</legend>
                    <div>
                    <label>Nama Karyawan</label>
                    <?php echo form_input('user', set_value('user')) ?>
                <input type="hidden" name="user_id" value="<?php echo set_value('user_id') ?>" class="ac_hidden" />
                
					</div>
					<div>
                    <label>Jabatan</label>
                    <?php echo xform_lookup('level_id','level'); ?>
                    </div>
                   
                    <div>
                    <label>Reporting Manager</label>
                  			<?php echo form_input('manager', set_value('manager')) ?>
		                <input type="hidden" name="manager_id" value="<?php echo set_value('manager_id') ?>" class="ac_hidden" />
		                
                    </div>
                    <div>
                    <label>Dinilai sebagai</label>
                     	<?php echo xform_lookup('rated_by_id','rated_by'); ?>
                    </div>
                    <div>
                    <label>Periode Penilaian(Awal)</label>
			            <?php echo xform_date('period_start') ?>
                    </div>
                    <div>
                    <label>Periode Penilaian(Akhir)</label>
                             <?php echo xform_date('period_end') ?>
                    </div>
                    
            </fieldset>
   			<fieldset id="prod">
       			<legend>Produktifitas</legend>
                    <div>
                    <label>Membuat target yang realistis</label>
                    	<?php echo xform_radio('prod_create_target',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Memenuhi batas waktu</label>
                    		<?php echo xform_radio('prod_achive',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mencari efisiensi</label>
                    	<?php echo xform_radio('prod_good_job',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mampu menangani banyak project bersamaan</label>
                    <?php echo xform_radio('prod_concurrent_project',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Produktifitas pekerjaan rutin</label>
                    <?php echo xform_radio('prod_routine_activity',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Waktu respon masalah</label>
                    <?php echo xform_radio('prod_respon_time',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Waktu penyelesaian masalah</label>
                    <?php echo xform_radio('prod_resolution',$criteria_options) ?>
                    </div>
                    
                    <div>
                         <label>Endurance(ketahanan)</label>
                         <?php echo xform_radio('prod_endurance',$criteria_options) ?>
                    </div>
               </fieldset>
               <fieldset id="com">
               	<legend>Komunikasi</legend>  
                    <div>
                    <label>Mengolah informasi yang diterima</label>
                    <?php echo xform_radio('com_inform_process',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mendengarkan pihak lain </label>
                    	<?php echo xform_radio('com_listening',$criteria_options) ?>
                    </div>
                     <div>
                    <label>Etika Berkomunikasi</label>
                    <?php echo xform_radio('com_ethic',$criteria_options) ?>
                    </div>
                    
                    <div>
                    <label>Berkomunikasi terstruktur</label>
                    <?php echo xform_radio('com_structure',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Kemampuan berbicara didepan umum</label>
                    <?php echo xform_radio('com_front',$criteria_options) ?>
                    </div>
                    
                    <div>
                    <label>Kemampuan Verbal</label>
                    <?php echo xform_radio('com_verb',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Kemampuan tulisan</label>
                    <?php echo xform_radio('com_write',$criteria_options) ?>
                    </div>
                    
                     <div>
                    <label>Kemampuan Verbal(Bahasa asing)</label>
                    <?php echo xform_radio('com_verb_a',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Kemampuan tulisan(Bahasa asing)</label>
                    <?php echo xform_radio('com_write_a',$criteria_options) ?>
                    </div>
                </fieldset>
                
               <fieldset id="lead">
                 	<legend>Kepemimpinan</legend>   
                    <div>
                    <label>Memimpin dengan memberikan Contoh</label>
                    <?php echo xform_radio('lead_example',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mencari solusi yang realistis</label>
                    <?php echo xform_radio('lead_solution',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Bertindak tegas, menghadapi masalah scr langsung</label>
                    <?php echo xform_radio('lead_firm',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mengupayakan yang terbaik untuk anggota tim</label>
                    <?php echo xform_radio('lead_best',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Menyelesaikan Konflik</label>
                    <?php echo xform_radio('lead_solve_conflict',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Membuat harapan yang jelas</label>
                    <?php echo xform_radio('lead_clear_hope',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mendelegasikan dengan jelas</label>
                    <?php echo xform_radio('lead_delegate',$criteria_options) ?>
                    </div>
                     </fieldset>
               <fieldset id="prive">
                 	<legend>Pengembangan Pribadi</legend>
                    <div>
                    <label>Tetap tenang dibawah tekanan</label>
                    <?php echo xform_radio('prive_cool',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Menetapkan standar yang tinggi u/ diri sendiri</label>
                    <?php echo xform_radio('prive_high_std',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Menetapkan tujuan yang menantang</label>
                    <?php echo xform_radio('prive_challenge',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Kreatif dan inisiatif</label>
                    <?php echo xform_radio('prive_creative',$criteria_options) ?>
                    </div>
               </fieldset>
               
               <fieldset id="rel">
                 	<legend>Hubungan</legend>  
                    <div>
                    <label>Mengerti kemauan client</label>
                    <?php echo xform_radio('rel_client',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mengesampingkan prasangka</label>
                    <?php echo xform_radio('rel_assumtion',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Memiliki semangat kerjasama tim(TeamWork)</label>
                    <?php echo xform_radio('rel_teamwork',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Memberi nasihat yang baik dan praktis</label>
                    <?php echo xform_radio('rel_suggest',$criteria_options) ?>
                    </div>
                    <div>
                      <label>Memiliki loyalitas</label>
                    <?php echo xform_radio('rel_loyalty',$criteria_options) ?>
                    </div>
                    <div>
                      <label>Memiliki tanggung jawab</label>
                    <?php echo xform_radio('rel_responsibility',$criteria_options) ?>
                    </div>
                    
                    </fieldset>
               <fieldset id="mng">
                 	<legend>Management</legend> 
                    <div>
                    <label>Memprioritaskan Tugas</label>
                    <?php echo xform_radio('mng_job_priority',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Menangani permasalahan dengan cepat dan baik</label>
                    <?php echo xform_radio('mng_problem',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mengembangkan strategi baru</label>
                    <?php echo xform_radio('mng_new_strategy',$criteria_options) ?>
                    </div>
                    <div>
                    <label>Mengatur Tugas</label>
                    <?php echo xform_radio('mng_role_set',$criteria_options) ?>
                    </div>
                    </fieldset>
               <fieldset>
                 	<legend>Summary</legend> 
                    <div>
                    <label>Rata-rata</label>
                          <input type="text" value="" id="average" name="average" class="number" readonly="readonly" />
                    </div>
                    <div>
                    <label>Catatan</label>
                          <textarea rows="" cols="" name="notes"></textarea>
                    </div>
                    
                    <div>
                    <label>Tanggal Penilaian</label>
                    	<?php echo xform_date('judge_date',array('default_today'=>true)) ?>
                    </div>
				</fieldset>
    <div class="action-buttons">
        <input type="submit" />
    </div>
</form> 