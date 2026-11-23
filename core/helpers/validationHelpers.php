<?php
declare(strict_types=1);

/**
 * Input validation helpers.
 *
 * Example:
 *   $errors = validate($_POST, [
 *       'fullname' => 'required|max:255',
 *       'myID'     => 'required|myid',
 *       'email'    => 'required|email',
 *       'phone'    => 'phone',
 *       'role'     => 'required|in:1,2,3',
 *       'password' => 'required|min:8',
 *       'confirm'  => 'required|same:password',
 *   ], ['myID' => 'IC number']);
 *
 *   if ($errors) { jsonError('Please check the form.', 422, $errors); }
 *
 * Rules: required, email, min:n, max:n, numeric, integer, date, myid, phone, in:a,b,c, same:field
 * A field without "required" is skipped when it is empty.
 */

if (!function_exists('isValidEmail')) {
    function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}

if (!function_exists('isValidPhone')) {
    /** Malaysian mobile number, e.g. 0123456789, 012-345 6789 or +60123456789. */
    function isValidPhone(string $phone): bool
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';
        if (str_starts_with($digits, '60')) {
            $digits = '0' . substr($digits, 2);
        }
        return (bool) preg_match('/^01\d{8,9}$/', $digits);
    }
}

if (!function_exists('validate')) {
    /**
     * Check data against rules.
     *
     * @param array<string,mixed>  $data   Usually $_POST.
     * @param array<string,string> $rules  field => "rule|rule:param"
     * @param array<string,string> $labels field => friendly name used in messages
     * @return array<string,string> field => first error message. Empty array = all good.
     */
    function validate(array $data, array $rules, array $labels = []): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;
            $value = is_string($value) ? trim($value) : $value;
            $label = $labels[$field] ?? ucfirst(str_replace('_', ' ', $field));
            $list  = explode('|', $ruleString);
            $empty = isBlank($value) || $value === [];

            // Optional field that is empty: nothing to check
            if ($empty && !in_array('required', $list, true)) {
                continue;
            }

            foreach ($list as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, '');
                $text = (string) $value;

                $message = match ($name) {
                    'required' => $empty ? "{$label} is required." : null,
                    'email'    => !isValidEmail($text) ? "{$label} must be a valid email address." : null,
                    'min'      => mb_strlen($text) < (int) $param ? "{$label} must be at least {$param} characters." : null,
                    'max'      => mb_strlen($text) > (int) $param ? "{$label} must not be more than {$param} characters." : null,
                    'numeric'  => !is_numeric($text) ? "{$label} must be a number." : null,
                    'integer'  => filter_var($text, FILTER_VALIDATE_INT) === false ? "{$label} must be a whole number." : null,
                    'date'     => !isValidDate($text) ? "{$label} must be a valid date." : null,
                    'myid'     => !isValidMyID($text) ? "{$label} must be a valid 12-digit IC number." : null,
                    'phone'    => !isValidPhone($text) ? "{$label} must be a valid phone number." : null,
                    'in'       => !in_array($text, explode(',', $param), true) ? "{$label} has an invalid value." : null,
                    'same'     => $text !== (string) ($data[$param] ?? '') ? "{$label} does not match." : null,
                    default    => null,
                };

                // Stop at the first problem of each field
                if ($message !== null) {
                    $errors[$field] = $message;
                    break;
                }
            }
        }
        return $errors;
    }
}
