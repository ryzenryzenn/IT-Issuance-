<?php

namespace App\Enums;

/**
 * Single source of truth for every permission in the system.
 * The backing string is the exact Spatie permission name used by
 * policies (`$user->can(...)`) and Blade (`@can(...)`).
 */
enum Permission: string
{
    // Assets
    case ViewAssets     = 'view assets';
    case CreateAssets   = 'create assets';
    case UpdateAssets   = 'update assets';
    case DeleteAssets   = 'delete assets';
    case TransferAssets = 'transfer assets';

    // Companies
    case ViewCompanies   = 'view companies';
    case CreateCompanies = 'create companies';
    case UpdateCompanies = 'update companies';
    case DeleteCompanies = 'delete companies';

    // Categories
    case ViewCategories   = 'view categories';
    case CreateCategories = 'create categories';
    case UpdateCategories = 'update categories';
    case DeleteCategories = 'delete categories';

    // Locations
    case ViewLocations   = 'view locations';
    case CreateLocations = 'create locations';
    case UpdateLocations = 'update locations';
    case DeleteLocations = 'delete locations';

    // Asset models
    case ViewAssetModels   = 'view asset models';
    case CreateAssetModels = 'create asset models';
    case UpdateAssetModels = 'update asset models';
    case DeleteAssetModels = 'delete asset models';

    // Employees
    case ViewEmployees   = 'view employees';
    case CreateEmployees = 'create employees';
    case UpdateEmployees = 'update employees';
    case DeleteEmployees = 'delete employees';

    // Users
    case ViewUsers   = 'view users';
    case CreateUsers = 'create users';
    case UpdateUsers = 'update users';
    case DeleteUsers = 'delete users';

    // Roles
    case ViewRoles   = 'view roles';
    case CreateRoles = 'create roles';
    case UpdateRoles = 'update roles';
    case DeleteRoles = 'delete roles';

    // Board (tickets / sticky notes)
    case ViewTickets   = 'view tickets';
    case CreateTickets = 'create tickets';
    case UpdateTickets = 'update tickets';
    case DeleteTickets = 'delete tickets';

    // Cross-cutting
    case ViewAuditLogs             = 'view audit logs';
    case UploadAccountabilityFiles = 'upload accountability files';
    case DeleteAccountabilityFiles = 'delete accountability files';
    case ExportReports             = 'export reports';
    case ViewTrash                 = 'view trash';
    case RestoreRecords            = 'restore records';
    case ForceDeleteRecords        = 'force delete records';

    /** All permission names. */
    public static function values(): array
    {
        return array_map(fn (self $p) => $p->value, self::cases());
    }

    /** Map a list of enum cases to their string names (for syncPermissions). */
    public static function names(array $permissions): array
    {
        return array_map(fn (self $p) => $p->value, $permissions);
    }
}
