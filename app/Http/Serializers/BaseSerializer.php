<?php

namespace App\Http\Serializers;

class BaseSerializer {
  protected $ids = [];
  protected $basic = [];
  protected $full = [];
  protected $private = [];

  public function list($users, $options = []) {
    return collect($users)->map(function($user) use ($options) {
      return $this->one($user, $options);
    });
  }

  public function one($model, $options = []) {
    $json = [];

    // Always returns the ids
    foreach ($this->ids as $field) {
      $config = $this->getField($model, $field);
      $json[$config['name']] = $config['value'];
    }

    foreach ($options as $value) {
      $group = $this->{$value};
      
      if (isset($group)) {
        foreach ($group as $field) {
          $config = $this->getField($model, $field);
          // Only return the prop that contains a value
          if ($config['value']) {
            $json[$config['name']] = $config['value'];
          }
        }
      }
    }

    return $json;
  }

  public function paginator($data) {
    return [
      'count'=> $data->count(),
      'current' => $data->currentPage(),
      'perPage' => $data->perPage(),
      'total'=> $data->total(),
    ];
  }

  /**
   * Receives a model and a field configuration as a parameter.
   * Returns an array with name of the field and value for the current field.
   */
  private function getField($model, $field) {
    $result = [];

    if (is_array($field)) {
      $parseMethod = 'parse'.ucfirst($field['mapping']);
      $customField = $field['mapping'];

      if (isset($field['name'])) {
        $customField = $field['name'];
      }

      $result['name'] = $customField;
      if (method_exists($this, $parseMethod)) {
        $result['value'] = $this->{$parseMethod}($model);
      } else if (is_array($model)) {
        // Sometimes we might receive arrays instead of objects
        $result['value'] = $model[$field['mapping']];
      } else {
        $result['value'] = $model->{$field['mapping']};
      }
    } else {
      $parseMethod = 'parse'.ucfirst($field);

      $result['name'] = $field;
      if (method_exists($this, $parseMethod)) {
        $result['value'] = $this->{$parseMethod}($model);
      } else if (is_array($model)) {
        // Sometimes we might receive arrays instead of objects
        $result['value'] = $model[$field];
      } else {
        $result['value'] = $model->{$field}; 
      }
    }

    return $result;
  }
}