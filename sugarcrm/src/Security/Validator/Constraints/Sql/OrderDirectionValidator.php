<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

namespace Sugarcrm\Sugarcrm\Security\Validator\Constraints\Sql;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 *
 * SQL order direction validator
 *
 */
class OrderDirectionValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof OrderDirection) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\OrderDirection');
        }

        // check for string
        if (!is_string($value)) {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($value)
                ->setCode(OrderBy::ERROR_STRING_REQUIRED)
                ->addViolation();
            return;
        }

        if (strtolower($value) !== 'asc' && strtolower($value) !== 'desc') {
            $this->context->buildViolation($constraint->message)
                ->setInvalidValue($value)
                ->setCode(OrderBy::ERROR_ILLEGAL_FORMAT)
                ->addViolation();
            return;
        }
    }
}
