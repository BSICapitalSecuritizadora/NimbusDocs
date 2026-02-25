<?php

/**
 * NimbusDocs — PHP-CS-Fixer Configuration
 * 
 * Rules based on PSR-12 with additional best practices.
 * Run: vendor/bin/php-cs-fixer fix
 * Dry-run: vendor/bin/php-cs-fixer fix --dry-run --diff
 */

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/bootstrap')
    ->in(__DIR__ . '/bin')
    ->name('*.php')
    // Exclude view templates (contain mixed PHP/HTML)
    ->exclude('Presentation/View')
    ->exclude('Presentation/Email')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false) // NO risky rules — never alter behavior
    ->setRules([
        // PSR-12 preset
        '@PSR12' => true,

        // Import ordering
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'no_unused_imports' => true,

        // Arrays
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline' => ['elements' => ['arrays']],
        'trim_array_spaces' => true,
        'no_whitespace_before_comma_in_array' => true,
        'whitespace_after_comma_in_array' => true,

        // Spacing
        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],
        'not_operator_with_successor_space' => false,
        'object_operator_without_whitespace' => true,
        'unary_operator_spaces' => true,
        'no_extra_blank_lines' => ['tokens' => [
            'extra',
            'throw',
            'use',
        ]],
        'no_trailing_whitespace' => true,
        'no_whitespace_in_blank_line' => true,

        // Blank lines
        'blank_line_before_statement' => ['statements' => [
            'return',
            'throw',
            'try',
        ]],
        'single_blank_line_at_eof' => true,

        // Class/function structure
        'class_attributes_separation' => ['elements' => [
            'method' => 'one',
            'property' => 'one',
        ]],
        'single_class_element_per_statement' => true,
        'no_blank_lines_after_class_opening' => true,

        // Casts
        'cast_spaces' => ['space' => 'single'],
        'lowercase_cast' => true,
        'short_scalar_cast' => true,

        // Comments
        'single_line_comment_style' => ['comment_types' => ['hash']],
        'no_empty_comment' => true,

        // Control structures
        'no_unneeded_control_parentheses' => true,
        'no_useless_else' => true,

        // Semicolons
        'no_empty_statement' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'multiline_whitespace_before_semicolons' => ['strategy' => 'no_multi_line'],

        // Strings
        'single_quote' => true,

        // Misc
        'no_trailing_comma_in_singleline' => true,
        'normalize_index_brace' => true,
    ])
    ->setFinder($finder)
;
