<?php
declare(strict_types=1);

namespace Perspective\B2BCheckout\Magewire\Checkout;

use Magewirephp\Magewire\Component;

/**
 * CompanyInfo Magewire.
 */
class CompanyInfo extends Component
{

    public ?string $method = null;

    public string $vatNumber = '';

    public string $companyName = '';

    public bool $saved = false;

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->companyName = ('Company Default Name');
        $this->vatNumber = ('Vat Number Default Value');
    }
}
