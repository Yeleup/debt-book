<?php

namespace App\Validator\UserOrganization;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class UniqueUserOrganization extends Constraint
{
    /*
     * Any public properties become valid options for the annotation.
     * Then, use these in your validator class.
     */
    public string $message = 'This "{{ value }}" has already sent a request to this organization';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
