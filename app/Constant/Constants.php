<?php

namespace App\Constant;

class Constants
{
  public const CLIENT_TYPE = [
    'yearly', 'monthly'
  ];
  public const ACTIVE = 1;
  public const DEACTIVE = 0;
  public const BLOCK_COUNT = 3;
  public const USER_GROUP =  ['super_admin', 'admin_user', 'merchant_admin', 'merchant_user',];
  public const USER_GROUP_ROUTE =  [
    'super_admin' => 'super-admin',
    'admin_user' => 'admin-user',
    'merchant_admin' => 'merchant-admin',
    'merchant_user' => 'merchant-user',
    'employee' => 'employee',
  ];
  public const PARENT_ID = 0;
  public const BANGLADESH = 19;
  public const THUMBNAIL_WEIGHT  = 400;
  public const THUMBNAIL_HEIGHT = 500;

  /* TABLE NAME START */
  public const TABLE_USERS = 'users';
  public const TABLE_STATE = 'state';
  public const TABLE_COUNTRIES = 'countries';
  public const TABLE_CLIENT  = 'client';
  /* TABLE NAME END */
}
