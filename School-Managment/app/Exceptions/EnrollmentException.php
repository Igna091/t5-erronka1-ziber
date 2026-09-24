<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * A student can't be enrolled in a course (course full, inactive, already enrolled...).
 * The message is shown to the user.
 */
class EnrollmentException extends RuntimeException
{
}
