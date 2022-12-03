<?php

class SmsCampaignController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function smsCampaign()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('smsCampaign.access')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['smsGroup'] = SmsGroup::getAll();
        return View::make('smsCampaign/list', $data);
    }

    public function smsCampaignDownloadTemplate()
    {
        Excel::create('campaign ' . date('Y-m-d His'), function ($excel) {
            // Set the title
            $excel->setTitle('campaign ' . date('Y-m-d H-i-s'));
            $excel->sheet('campaign', function ($sheet) {
                $sheet->setColumnFormat(array(
                    'A' => '@'
                ));
                $row1 = array(
                    'phone', 'name', 'email'
                );
                $sheet->row(1, $row1);
//                $sheet->setAutoSize(true);
            });

        })->download('xlsx');
    }

    public function smsCampaignSendNewGroup()
    {
        $inputs = Input::except('_token');
        if (!empty($inputs['template'])) {
            $file = Input::file('template');
            $filename = date('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path() . '/excel/clinics';
            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                $inputs['template'] = 'excel/clinics/' . $filename;
            } else {
                Flash::error("The File Not Uploaded Correctly");
                return Redirect::back();
            }
        } else {
            Flash::error("Import Template File Is Required");
            return Redirect::back();
        }
        ini_set('max_execution_time', 3000);
        $dataArray = array();
        Excel::load($inputs['template'], function ($reader) use ($inputs, &$dataArray) {
            $sheet = $reader->toArray();
            $phonesArray = array();
            foreach ($sheet as $key => $val) {
                if (isset($val['phone']) && $val['phone']) {
                    if (in_array($val['phone'], $phonesArray)) {
                        continue;
                    } else {
                        $phonesArray[] = $val['phone'];
                    }
                    $dataArray[$key]['phone'] = $val['phone'];
                    $dataArray[$key]['name'] = isset($val['name']) ? $val['name'] : null;
                    $dataArray[$key]['email'] = isset($val['email']) ? $val['email'] : null;
                }
            }
        });
        if (isset($dataArray[0]) && $dataArray[0]) {
            $smsGroup = SmsGroup::add(array(
                'name' => $inputs['group_name']
            ));
            foreach ($dataArray as $key => $val) {
                $smsPhone = SmsPhones::add(array(
                    'sms_group_id' => $smsGroup['id'],
                    'phone' => $val['phone'],
                    'name' => $val['name'],
                    'email' => $val['email'],
                ));
                SmsCampaign::add(array(
                    'campaign_name' => $inputs['campaign_name'],
                    'sms_phone_id' => $smsPhone['id'],
//                    'message' => str_replace(PHP_EOL, '\n', $inputs['message']),
                    'message' => $inputs['message'],
                ));
            }
            Flash::success('Message Sent Successfully');
        } else {
            Flash::error('Message Not Sent, No Phones In The File!');
        }
        unlink($inputs['template']);
        return Redirect::back();
    }

    public function smsCampaignSendExistGroup()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, array(
            'group_id' => 'required',
            'campaign_name' => 'required',
            'message' => 'required',
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $phones = SmsPhones::getByGroupId($inputs['group_id']);
                foreach ($phones as $key => $val) {
                    SmsCampaign::add(array(
                        'campaign_name' => $inputs['campaign_name'],
                        'sms_phone_id' => $val['id'],
                        'message' => str_replace(PHP_EOL, '\n', $inputs['message']),
                    ));
                }
                Flash::success('Message Sent Successfully');
                return Redirect::back();
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
    }
}
