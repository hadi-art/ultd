<div class="card">
    <div class="card-body">
        <form action="" name="site-config-form" method="POST">
        {{ csrf_field() }}
        <!-- kiple box begin -->
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div>LP </div>
                        <hr>
                    </div>

                </div>
                <!-- kiple box over -->





                <!-- common start -->
                <div class="card-body">
                    <div class="card-title">
                        <div>COMMON</div>
                        <hr>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>SITE ID</label>
                                <input type="text" required name="common[site_id]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>LPRLA URL</label>
                                <input type="text" required name="common[lprla_url]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>SITE NAME</label>
                                <input type="text" required name="common[site_name]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>DATABASE CHECK NEXT WAIT (millisecond)</label>
                                <input type="text" required placeholder="sleep x milliseconds to wait other camera's push" name="common[database_check_next_wait]" value="{{$sites['common']['database_check_next_wait']??old('database_check_next_wait')}}" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                            {{--<div class="form-group">
                                <label>ALL CHARGEABLE</label>
                                <select class="select2 form-control" name="common[all_chargeable]">
                                    <option value="1" @if (!isset($sites['common']['all_chargeable']) || (($sites['common']['all_chargeable'] ?? old('all_chargeable')) == 1)) selected @endif>yes</option>
                                    <option value="2" @if (($sites['common']['all_chargeable'] ?? old('all_chargeable')) == 2)selected @endif>no</option>
                                </select>
                                <i class="form-group__bar"></i>
                            </div>--}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>DB HOST</label>
                                <input type="text" required name="common[db_host]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                        <div class="col">

                            <div class="form-group">
                                <label><i class="must_input_i">*</i>DB NAME</label>
                                <input type="text" required name="common[db_name]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>DB USER</label>
                                <input type="text" required name="common[db_user]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>VENDOR ID</label>
                                <input type="text" required name="common[vendor_id]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>DB PASSWORD</label>
                                <input type="password" required name="common[db_pswd]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                            <div class="form-group">
                                <label><i class="must_input_i">*</i>VENDOR API URL</label>
                                <input type="text" name="common[vendor_api_url]" value="" class="form-control"/>
                                <i class="form-group__bar"></i>
                            </div>
                        </div>
                    </div>



                </div>
                <!-- common start -->


                <div class="form-group">
                    <button type="submit" class="btn btn-primary">save</button>
                </div>
            </div>
        </form>
    </div>
</div>