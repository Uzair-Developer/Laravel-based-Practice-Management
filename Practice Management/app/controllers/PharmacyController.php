<?php

use core\enums\AttributeType;

class PharmacyController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function pharmacyList()
    {
        if (!$this->user->hasAccess('pharmacy.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $reception = IpToReception::getAll([
            'ip' => Functions::GetClientIpJsOrPhp(),
            'getFirst' => true
        ]);

        if ($this->user->hasAccess('pharmacy.next_patient') || $this->user->hasAccess('admin')) {
            $array = [
                'hospital_id' => 1,
                'date' => date('Y-m-d'),
                'call_flag' => '0',
                'getCount' => true,
            ];
            if ($reception) {
                $array['pharmacy_ticket_type_ids'] = explode(',', $reception['pharmacy_ticket_type_id']);
                $data['pharmacy_desk_num'] = '(Desk No. ' . $reception['name'] . ')';

            } else {
                $array['pharmacy_ticket_type_ids'] = '0';
                $data['pharmacy_desk_num'] = '(Error)';
            }
            $data['queue_count'] = PharmacyQueue::getAll($array);

            $data['queue_count_served'] = PharmacyQueue::getAll([
                'hospital_id' => 1,
                'date' => date('Y-m-d'),
                'call_flag' => '2',
                'call_done_pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
                'getCount' => true,
            ]);
        }

        $data['queue_call'] = PharmacyQueue::getAll([
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            'call_flag' => 1, // call
        ]);

        $array = [
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => 3, // pass
            'getCount' => true,
        ];
        if ($reception) {
            $array['pharmacy_ticket_type_ids'] = explode(',', $reception['pharmacy_ticket_type_id']);
        } else {
            $array['pharmacy_ticket_type_ids'] = '0';
        }
        $data['queue_pass'] = PharmacyQueue::getAll($array);
        $data['ticketType'] = AttributePms::getAll(AttributeType::$pmsReturn['PharmacyTicketType']);
        return View::make('pharmacy/list', $data);
    }

    public function getQueuePharmacyCounts()
    {
        $reception = IpToReception::getAll([
            'ip' => Functions::GetClientIpJsOrPhp(),
            'getFirst' => true
        ]);

        $array = [
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => '0',
            'getCount' => true,
        ];
        if ($reception) {
            $array['pharmacy_ticket_type_ids'] = explode(',', $reception['pharmacy_ticket_type_id']);
        } else {
            $array['pharmacy_ticket_type_ids'] = '0';
        }
        $data['queue_count'] = PharmacyQueue::getAll($array);

        $data['count_served'] = PharmacyQueue::getAll([
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => '2',
            'pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            'getCount' => true,
        ]);

        $array = [
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => 3, // pass
            'getCount' => true,
        ];
        if ($reception) {
            $array['pharmacy_ticket_type_ids'] = explode(',', $reception['pharmacy_ticket_type_id']);
        } else {
            $array['pharmacy_ticket_type_ids'] = '0';
        }
        $data['count_pass'] = PharmacyQueue::getAll($array);
        return $data;
    }

    public function getNextQueuePharmacy()
    {
        $reception = IpToReception::getAll([
            'ip' => Functions::GetClientIpJsOrPhp(),
            'getFirst' => true
        ]);
        $array = [
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => '0',
            'orderBy' => ['id', 'asc'],
            'getFirst' => true,
        ];
        if ($reception) {
            $array['pharmacy_ticket_type_ids'] = explode(',', $reception['pharmacy_ticket_type_id']);
        } else {
            $array['pharmacy_ticket_type_ids'] = '0';
        }
        $firstQueue = PharmacyQueue::getAll($array);
        if ($firstQueue) {
            PharmacyQueue::edit([
                'call_flag' => 1,
                'call_by' => $this->user->id,
                'call_datetime' => date('Y-m-d H:i:s'),
                'pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
                'call_pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            ], $firstQueue['id']);
            Flash::success('You Called Patient No. ' . $firstQueue['queue_code']);
            return Redirect::back();
        } else {
            Flash::error('No Patients In Queue!');
            return Redirect::back();
        }
    }

    public function callDonePharmacyQueue($id)
    {
        $inputs = Input::except('_token');
        $queue = PharmacyQueue::getById($id);
        if ($queue && ($queue['call_flag'] == 1 || $queue['call_flag'] == 3)) {
            PharmacyQueue::edit([
                'call_flag' => 2,
                'call_done_by' => $this->user->id,
                'call_done_datetime' => date('Y-m-d H:i:s'),
                'call_done_pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            ], $queue['id']);
            Flash::success('Updated Successfully');
            if (isset($inputs['with_next']) && $inputs['with_next']) {
                return $this->getNextQueuePharmacy();
            } elseif (isset($inputs['with_call_from_pass']) && $inputs['with_call_from_pass']) {
                if (isset($inputs['id']) && $inputs['id']) {
                    $url = route('callFromPassPharmacyQueue') . '?id=' . $inputs['id'];
                    return Redirect::to($url);
                } else {
                    return Redirect::back();
                }
            } else {
                return Redirect::back();
            }
        } else {
            Flash::error('Error In Patient Queue Number!');
            return Redirect::back();
        }
    }

    public function patientPassPharmacyQueue($id)
    {
        $inputs = Input::except('_token');
        $queue = PharmacyQueue::getById($id);
        if ($queue && $queue['call_flag'] == 1) {
            PharmacyQueue::edit([
                'call_flag' => 3,
                'pass_datetime' => date('Y-m-d H:i:s'),
            ], $queue['id']);
            Flash::success('Updated Successfully');
            if (isset($inputs['with_next']) && $inputs['with_next']) {
                return $this->getNextQueuePharmacy();
            } elseif (isset($inputs['with_call_from_pass']) && $inputs['with_call_from_pass']) {
                if (isset($inputs['id']) && $inputs['id']) {
                    $url = route('callFromPassPharmacyQueue') . '?id=' . $inputs['id'];
                    return Redirect::to($url);
                } else {
                    return Redirect::back();
                }
            } else {
                return Redirect::back();
            }
        } else {
            Flash::error('Error In Patient Queue Number!');
            return Redirect::back();
        }
    }

    public function checkNextQueuePharmacy()
    {
        $firstQueue = PharmacyQueue::getAll([
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => 1,
            'orderBy' => ['id', 'asc'],
            'pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            'getFirst' => true,
        ]);
        if ($firstQueue) {
            $data['success'] = 'no';
            $data['queue'] = $firstQueue->toArray();
        } else {
            $data['success'] = 'yes';
        }
        return $data;
    }

    public function refreshQueuePass()
    {
        $reception = IpToReception::getAll([
            'ip' => Functions::GetClientIpJsOrPhp(),
            'getFirst' => true
        ]);
        $array = [
            'hospital_id' => 1,
            'date' => date('Y-m-d'),
            'call_flag' => 3, // pass
        ];
        if ($reception) {
            $array['pharmacy_ticket_type_ids'] = explode(',', $reception['pharmacy_ticket_type_id']);
        } else {
            $array['pharmacy_ticket_type_ids'] = '0';
        }
        $data['queue_pass'] = PharmacyQueue::getAll($array);
        return View::make('pharmacy/pass_table', $data)->render();
    }

    public function callFromPassPharmacyQueue()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['id']) && $inputs['id']) {
            $queue = PharmacyQueue::getById($inputs['id']);
            PharmacyQueue::edit([
                'call_flag' => 1,
                'call_from_pass_by' => $this->user->id,
                'call_from_pass_datetime' => date('Y-m-d H:i:s'),
                'call_from_pass_pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
                'pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            ], $inputs['id']);
            Flash::success('You Called Patient No. ' . $queue['queue_code']);
            return Redirect::back();
        } else {
            Flash::error('Missing Data!');
            return Redirect::back();
        }
    }

    public function cancelPatientPharmacyQueue($id)
    {
        $inputs = Input::except('_token');
        $queue = PharmacyQueue::getById($id);
        if ($queue && ($queue['call_flag'] == 1 || $queue['call_flag'] == 3)) {
            PharmacyQueue::edit([
                'call_flag' => 4,
                'cancel_by' => $this->user->id,
                'cancel_datetime' => date('Y-m-d H:i:s'),
                'cancel_pharmacy_ip' => Functions::GetClientIpJsOrPhp(),
            ], $queue['id']);
            Flash::success('Updated Successfully');
            if (isset($inputs['with_next']) && $inputs['with_next']) {
                return $this->getNextQueuePharmacy();
            } elseif (isset($inputs['with_call_from_pass']) && $inputs['with_call_from_pass']) {
                if (isset($inputs['id']) && $inputs['id']) {
                    $url = route('callFromPassPharmacyQueue') . '?id=' . $inputs['id'];
                    return Redirect::to($url);
                } else {
                    return Redirect::back();
                }
            } else {
                return Redirect::back();
            }
        } else {
            Flash::error('Error In Patient Queue Number!');
            return Redirect::back();
        }
    }

    public function pharmacyGetActivity()
    {
        $reception = IpToReception::getAll([
            'ip' => Functions::GetClientIpJsOrPhp(),
            'getFirst' => true
        ]);
        return $reception;
    }

    public function pharmacyChangeActivity()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['pharmacy_ticket_type_id']) && $inputs['pharmacy_ticket_type_id']) {
            $reception = IpToReception::getAll([
                'ip' => Functions::GetClientIpJsOrPhp(),
                'getFirst' => true
            ]);
            if(is_array($inputs['pharmacy_ticket_type_id'])) {
                IpToReception::edit([
                    'pharmacy_ticket_type_id' => implode(',', $inputs['pharmacy_ticket_type_id'])
                ], $reception['id']);
                Flash::success('Your activity changed successfully');
            } else {
                Flash::error('Error in activity field type, contact to administrator!');
            }
        } else {
            Flash::error('You must choose at least one activity!');
        }
        return Redirect::back();
    }
}
