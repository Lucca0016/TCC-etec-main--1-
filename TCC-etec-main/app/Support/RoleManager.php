<?php

namespace App\Support;

/**
 * RoleManager - Gerenciador centralizado de papéis e permissões
 * 
 * Única fonte de verdade para definições de roles no projeto.
 * Evita duplicação e garante consistência em toda a aplicação.
 */
class RoleManager
{
    private const ADMIN_ROLES = ['adm', 'administrador', 'admin'];
    private const PROFESSOR_ROLES = ['professor', 'prof', 'docente'];
    private const SECRETARIA_ROLES = ['secretaria', 'secretário', 'secretariao', 'secretaria_adj', 'funcionario'];
    private const STUDENT_ROLES = ['aluno', 'estudante', 'student'];

    public static function getAdminRoles(): array
    {
        return self::ADMIN_ROLES;
    }

    public static function getProfessorRoles(): array
    {
        return self::PROFESSOR_ROLES;
    }

    public static function getSecretariaRoles(): array
    {
        return self::SECRETARIA_ROLES;
    }

    public static function getStudentRoles(): array
    {
        return self::STUDENT_ROLES;
    }

    public static function isAdmin(string $role): bool
    {
        return in_array(strtolower($role), self::ADMIN_ROLES, true);
    }

    public static function isProfessor(string $role): bool
    {
        return in_array(strtolower($role), self::PROFESSOR_ROLES, true);
    }

    public static function isSecretaria(string $role): bool
    {
        return in_array(strtolower($role), self::SECRETARIA_ROLES, true);
    }

    public static function isStudent(string $role): bool
    {
        return in_array(strtolower($role), self::STUDENT_ROLES, true);
    }

    public static function profileMatches(?string $profile, string $role): bool
    {
        if ($profile === null || $profile === '') {
            return false;
        }

        $profile = strtolower($profile);
        $role    = strtolower($role);

        switch ($profile) {
            case 'admin':
                return self::isAdmin($role);
            case 'professor':
                return self::isProfessor($role);
            case 'secretaria':
                return self::isSecretaria($role);
            case 'aluno':
            case 'student':
                return self::isStudent($role);
            default:
                return false;
        }
    }

    public static function getDefaultRedirectForRole(string $role): string
    {
        $role = strtolower($role);

        if (self::isAdmin($role)) {
            return '/TCC-etec/admin';
        }
        if (self::isProfessor($role)) {
            return '/TCC-etec/professor';
        }
        if (self::isSecretaria($role)) {
            return '/TCC-etec/secretaria';
        }
        if (self::isStudent($role)) {
            return '/TCC-etec/aluno';
        }
        return '/TCC-etec/';
    }

    public static function normalize(string $role): string
    {
        return strtolower(trim($role));
    }

    public static function getGroup(string $role): ?string
    {
        $role = strtolower($role);

        if (self::isAdmin($role))      return 'admin';
        if (self::isProfessor($role))  return 'professor';
        if (self::isSecretaria($role)) return 'secretaria';
        if (self::isStudent($role))    return 'student';
        return null;
    }

    public static function isInGroup(string $role, string $group): bool
    {
        return self::getGroup($role) === strtolower($group);
    }
}
