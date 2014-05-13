<?php
/**
 * Author: RaymondChou
 * Date: 13-1-11
 * File: notify.php
 * Email: zhouyt.kai7@gmail.com
 */

class Notify_Task {

    public function run($arguments)
    {
        $tasks = UTask::
            where('sms_notify','=',1)
            ->or_where('push_notify','=',1)
            ->where('status','=',1)
            ->where('start_at', '<=', date('Y-m-d'))
            ->where('end_at', '>=', date('Y-m-d'))
            ->get(array('id','title','products','end_at','notify_before','push_notify','sms_notify'));

        foreach($tasks as $task)
        {
            if(!is_numeric($task->notify_before)) continue;
            if(strtotime(date('Y-m-d')) == strtotime($task->end_at) - 3600*24*$task->notify_before)
            {
                $user_tasks = UserTask::where('task_id','=',$task->id)
                    ->where('complete_status','=',0)
                    ->get(array('user_id'));

                foreach($user_tasks as $user_task)
                {
                    //推送提醒
                    if($task->push_notify == 1)
                    {
                        $message = new Message();
                        $message->to_user_id    = $user_task->user_id;
                        $message->is_system  = 1;
                        $message->content    = '学习任务提醒,请及时完成学习任务 - '.$task->title;
                        $message->status     = 1;
                        $message->save();

                        $msg_content = array(
                            'n_title'   => '您有一个任务提醒  江苏中行 M-Learning',
                            'n_content' => '您有一个任务提醒 江苏中行 M-Learning 点击查看',
                            'n_extras'  => array(
                                'action' => 'new_message',
                                'ios'    => array('badge' => 1)
                            )
                        );
                        Bundle::start('jpush');
                        $jpush = new Jpush();
                        $jpush->send(1235, 3, $user_task->user_id, 1, json_encode($msg_content));
                    }

                    //短信提醒
                    if($task->sms_notify == 1)
                    {
                        $sms_content = '江苏中行 M-Learning 提醒您,您有一个未完成的学习任务 - '.$task->title.' ,请及时完成';
                        Sms::send($user_task->user->phone, $sms_content);
                    }
                }
            }

        }
    }

}