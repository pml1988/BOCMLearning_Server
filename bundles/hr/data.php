<?php
/**
 * hr数据导入
 */

class HRDATA {

	public static function run($file_name)
    {

        Cache::forever('current_hr_file',$file_name);
        Cache::forever('current_hr_time',date('Y-m-d H:i:s'));

//        static::unzip();
        static::do_users();
//        static::do_bank_infos();
//        static::do_bank_types();
//        static::do_post_infos();
//        static::do_type_infos();

        Cache::forever('last_hr_file',$file_name);
        Cache::forever('last_hr_time',date('Y-m-d H:i:s'));
        Cache::forever('last_hr_status','已完成');

        Cache::forget('current_hr_status');
        Cache::forget('current_hr_time');
        Cache::forget('current_hr_file');
    }

    public static function do_type_infos()
    {
        if(!file_exists(path('storage').'hr_data/TYPEINFO.txt')) return;

        Cache::forever('current_hr_status','正在导入TypeInfo');

        $file_name = path('storage').'hr_data/TYPEINFO.txt';
        $fp = fopen($file_name,'r');
        //逐行读取
        while(!feof($fp))
        {
            $buffer = fgets($fp);
            if($buffer == '')
            {
                continue;
            }
            $buffer = iconv('GB18030','UTF-8',$buffer);

            //整理数据
            $data_array = explode('|',$buffer);

            $data['TYPENO']   = $data_array[0];
            $data['TYPENAME'] = $data_array[1];
            $data['BANKID']   = $data_array[2];
            $data['SORT']     = $data_array[3];

            if( mb_substr($data['BANKID'] , 0 , 4) == '1071')
            {
                continue;
            }

            //数据库操作
            $orm_obj = TypeInfo::where('TYPENO','=',$data['TYPENO'])->first();
            if($orm_obj != null)
            {
                //更新
                foreach($data as $key => $value)
                {
                    unset($data['TYPENO']);
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            else
            {
                //插入
                $orm_obj = new TypeInfo();
                foreach($data as $key => $value)
                {
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            unset($buffer);
            unset($data);
            unset($data_array);
        }
        fclose($fp);
        @unlink($file_name);

    }

    public static function do_post_infos()
    {
        if(!file_exists(path('storage').'hr_data/POSTINFO.txt')) return;

        Cache::forever('current_hr_status','正在导入PostInfo');

        $file_name = path('storage').'hr_data/POSTINFO.txt';
        $fp = fopen($file_name,'r');
        //逐行读取
        while(!feof($fp))
        {
            $buffer = fgets($fp);
            if($buffer == '')
            {
                continue;
            }
            $buffer = iconv('GB18030','UTF-8',$buffer);

            //整理数据
            $data_array = explode('|',$buffer);

            $data['POSTNO']   = $data_array[0];
            $data['POSTNAME'] = $data_array[1];
            $data['NAMETYPE'] = $data_array[2];
            $data['BANKID']   = $data_array[3];
            $data['SORT']     = $data_array[4];
            $data['POSTTYPE'] = $data_array[5];
            $data['WAGELVL']  = $data_array[6];
            $data['SPECNAME'] = $data_array[7];
            $data['UPPOSTNO'] = $data_array[8];
            $data['PCOUNT']   = $data_array[9];
            $data['UCOUNT']   = $data_array[10];
            $data['PFLAG']    = $data_array[11];
            $data['WAGELVL2'] = $data_array[12];

            if( mb_substr($data['BANKID'] , 0 , 4) == '1071')
            {
                continue;
            }

            //数据库操作
            $orm_obj = PostInfo::where('POSTNO','=',$data['POSTNO'])->first();
            if($orm_obj != null)
            {
                //更新
                foreach($data as $key => $value)
                {
                    unset($data['POSTNO']);
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            else
            {
                //插入
                $orm_obj = new PostInfo();
                foreach($data as $key => $value)
                {
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            unset($data);
            unset($data_array);
            unset($orm_obj);
            unset($buffer);
        }
        fclose($fp);
        @unlink($file_name);
    }

    public static function do_bank_types()
    {
        if(!file_exists(path('storage').'hr_data/BANKTYPE.txt')) return;

        Cache::forever('current_hr_status','正在导入BankType');

        $file_name = path('storage').'hr_data/BANKTYPE.txt';
        $fp = fopen($file_name,'r');
        //逐行读取
        while(!feof($fp))
        {
            $buffer = fgets($fp);
            if($buffer == '')
            {
                continue;
            }
            $buffer = iconv('GB18030','UTF-8',$buffer);

            //整理数据
            $data_array = explode('|',$buffer);

            $data['BANKTYPE'] = $data_array[0];
            $data['TYPENAME'] = $data_array[1];
            $data['TYPEPROP'] = $data_array[2];

            //数据库操作
            $orm_obj = BankType::where('BANKTYPE','=',$data['BANKTYPE'])->first();
            if($orm_obj != null)
            {
                //更新
                foreach($data as $key => $value)
                {
                    unset($data['BANKTYPE']);
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            else
            {
                //插入
                $orm_obj = new BankType();
                foreach($data as $key => $value)
                {
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            unset($buffer);
            unset($data);
            unset($data_array);
        }
        fclose($fp);
        @unlink($file_name);

    }

    public static function do_bank_infos()
    {
        if(!file_exists(path('storage').'hr_data/BANKINFO.txt')) return;

        Cache::forever('current_hr_status','正在导入BankInfo');

        $file_name = path('storage').'hr_data/BANKINFO.txt';
        $fp = fopen($file_name,'r');
        //逐行读取
        while(!feof($fp))
        {
            $buffer = fgets($fp);
            if($buffer == '')
            {
                continue;
            }
            $buffer = iconv('GB18030','UTF-8',$buffer);

            //整理数据
            $data_array = explode('|',$buffer);

            $data['BANKID']   = $data_array[0];
            $data['SNAME']    = $data_array[1];
            $data['BANKNAME'] = $data_array[2];
            $data['SBANKID']  = $data_array[3];
            $data['BANKRAND'] = $data_array[4];
            $data['BANKTYPE'] = $data_array[5];
            $data['BANKFLAG'] = $data_array[6];

            if( mb_substr($data['BANKID'] , 0 , 4) == '1071')
            {
                continue;
            }

            //数据库操作
            $orm_obj = BankInfo::where('BANKID','=',$data['BANKID'])->first();
            if($orm_obj != null)
            {
                //更新
                foreach($data as $key => $value)
                {
                    unset($data['BANKID']);
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            else
            {
                //插入
                $orm_obj = new BankInfo();
                foreach($data as $key => $value)
                {
                    $orm_obj->{$key} = $value;
                }
                $orm_obj->save();
            }
            unset($buffer);
            unset($data);
            unset($data_array);
        }
        fclose($fp);
        @unlink($file_name);

    }

    public static function do_users()
    {
        if(!file_exists(path('storage').'hr_data/Userview.txt')) return;

        Cache::forever('current_hr_status','正在导入UserView');

        $file_name = path('storage').'hr_data/Userview.txt';
        $fp = fopen($file_name,'r');
        //逐行读取
        while(!feof($fp))
        {
            $buffer = fgets($fp);
            if($buffer == '')
            {
                continue;
            }
            $buffer = iconv('GB18030','UTF-8',$buffer);

            //整理数据
            $data_array = explode('|',$buffer);

            $data['job_code']  = $data_array[0];
            $data['ehr_id']    = $data_array[1];
            $data['user_name'] = $data_array[2];
            $data['bank_id']   = $data_array[3];
            $data['password']  = $data_array[5];
            $data['status']    = $data_array[7];
            $data['CERTNO']    = $data_array[9];
            $data['SEX']       = $data_array[10];
            $data['BIRTH']     = $data_array[11];
            $data['POST']      = $data_array[12];
            $data['POSTMODE']  = $data_array[13];
            $data['POSTDATE']  = $data_array[14];
            $data['EDU']       = $data_array[15];
            $data['TECHTYPE']  = $data_array[16];
            $data['POLITY']    = $data_array[17];
            $data['WAGELVL']   = $data_array[18];
            $data['WORKMODE']  = $data_array[19];
            $data['WORKDATE']  = $data_array[20];
            $data['ENTERDATE'] = $data_array[21];
            $data['line']      = substr($data_array[3],0,4);

            if( mb_substr($data['bank_id'] , 0 , 4) == '1071')
            {
                continue;
            }

            //数据库操作
            $user = User::where('job_code','=',$data['job_code'])->first();
            if($user != null)
            {
                //更新
                foreach($data as $key => $value)
                {
                    unset($data['job_code']);
                    $user->{$key} = $value;
                }
                $user->save();
            }
            else
            {
                //插入
                $user = new User();
                foreach($data as $key => $value)
                {
                    $user->{$key} = $value;
                }
                $user->save();
            }
            unset($buffer);
            unset($data);
            unset($data_array);
        }
        fclose($fp);
        @unlink($file_name);

    }

    //解压缩上传包
    public static function unzip()
    {
        $path = path('storage').'hr_data/';
        $zip = $path.'HRUSERINFO20130111.zip';
        @exec('unzip -d '.$path.' '.$zip);
    }
	
}