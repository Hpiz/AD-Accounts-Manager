<?php


namespace App\Api\Ad;


use Adldap\Models\Group;
use App\Models\Database\DistrictDatabase;
use App\Models\User\PermissionHandler;
use App\Models\User\PermissionLevel;
use System\App\AppException;
use System\App\LDAPLogger;

class ADGroups extends ADApi
{

    /**
     *  This should be a unique samaccountname or DN
     *
     * @param $groupName
     */
    public static function getGroup($groupName, $baseDN = null)
    {
        if (is_null($baseDN) or $baseDN === '') {
            $baseDN = self::getOUFromDN(DistrictDatabase::getAD_BaseDN());
        }

        LDAPLogger::get()->info("Getting " . $groupName . " from Active Directory");
        if ($groupName instanceof Group) {
            return $groupName;
        }
        $conn = ADConnection::getConnectionProvider();
        $group = $conn
            ->search()
            ->groups()
            ->in($baseDN)
            ->where("samaccountname", '=', $groupName)
            ->limit(1)
            ->get()[0];


        LDAPLogger::get()->debug($group);
        if ($group == null || !$group->exists) {
            $group = $conn->search()->findByDn($groupName);
            LDAPLogger::get()->debug($group);
        }
        if ($group == null || !$group->exists) {
            throw new AppException('That group was not found.', AppException::GROUP_NOT_FOUND);
        }
        if (!PermissionHandler::hasPermission(self::getOUFromDN($group->getDistinguishedName()), PermissionLevel::GROUPS, PermissionLevel::GROUP_READ)) {
            throw new AppException('That group was not found.', AppException::FAIL_GROUP_READ_PERM);

        }
        return $group;
    }

    public static function listGroups(string $searchTerm)
    {
        //$andFilter = ["objectClass" => "user"];
        $groups = ADConnection::getConnectionProvider()->search()
            ->groups()
            ->select('samaccountname', 'dn')
            ->orWhereContains("name", $searchTerm)
            ->orWhereContains("mail", $searchTerm)
            ->orWhereContains("description", $searchTerm)
            ->orWhereContains("samaccountname", $searchTerm)
            // ->where($andFilter)
            ->get();
        $groupNames = [];
        /* @var $group \Adldap\Models\Group */
        foreach ($groups as $group) {
            if (PermissionHandler::hasPermission(self::getOUFromDN($group->getDistinguishedName()), PermissionLevel::GROUPS, PermissionLevel::GROUP_READ)) {
                $groupNames[] = $group->getAccountName();
            }
        }
        return $groupNames;
    }

    public static function createGroup(\App\Models\District\AddDistrictGroup $param)
    {

    }
}