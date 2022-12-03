<?php

use Laracasts\Flash\Flash;

class CountryController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();

    }

    public function listCountry()
    {
        if(!$this->user->hasAccess('country.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        if(!empty(Input::get('q'))){
            $data['countries'] = Country::getAllPaginateWithFilter(Input::get('q'));
        } else {
            $data['countries'] = Country::getAll();
        }
        return View::make('diagnosis/country/list', $data);
    }

    public function addCountry()
    {
        if(!$this->user->hasAccess('country.add') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['parents'] = Country::getParents();
        return View::make('diagnosis/country/add', $data);
    }

    public function createCountry()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Country::$rules);
        if ($validator->fails()) {
            Flash::error( $validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                Country::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listCountry');
    }

    public function editCountry($id)
    {
        if(!$this->user->hasAccess('country.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['parents'] = Country::getParents();
        $data['country'] = Country::getById($id);
        return View::make('diagnosis/country/edit', $data);
    }

    public function updateCountry($id)
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Country::$rules);
        if ($validator->fails()) {
            Flash::error( $validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                Country::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listCountry');
    }

    public function deleteCountry($id)
    {
        if(!$this->user->hasAccess('country.delete') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Country::remove($id);
        return Redirect::route('listCountry');
    }

    public function getCitiesOfCountry()
    {
        $country_id = Input::get('country_id');
        $cities = Country::getChildOfCountry($country_id);
        $html = "<option selected value=''>Choose</option>";
        foreach ($cities as $val) {
            $html .= "<option value='" . $val['id'] . "'>" . $val['name'] . "</option>";
        }
        return $html;
    }

    public function getCitiesOfCountryForEdit()
    {
        $country_id = Input::get('country_id');
        $city_id = Input::get('city_id');
        $cities = Country::getChildOfCountry($country_id);
        $html = "<option value=''>Choose</option>";
        foreach ($cities as $val) {
            if ($city_id == $val['id']) {
                $html .= "<option selected value='" . $val['id'] . "'>" . $val['name'] . "</option>";
            } else {
                $html .= "<option value='" . $val['id'] . "'>" . $val['name'] . "</option>";
            }
        }
        return $html;
    }
}