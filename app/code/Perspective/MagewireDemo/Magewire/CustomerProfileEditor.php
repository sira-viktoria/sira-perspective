<?php
declare(strict_types=1);

namespace Perspective\MagewireDemo\Magewire;

use Magewirephp\Magewire\Component\Form;
use Magewirephp\Magewire\Exception\AcceptableException;
use Psr\Log\LoggerInterface;
use Rakit\Validation\Validator;

/**
 * CustomerProfileEditor Class.
 */
class CustomerProfileEditor extends Form
{
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    public bool $componentInitialized = false;
    public string $firstname = '';
    public string $lastname = '';
    public string $telephone = '';

    /**
     * @var array
     */
    protected $rules = [
        'firstname' => 'required|min:2|max:50',
        'lastname'  => 'required|min:2|max:50',
        'telephone' => ['required']
    ];

    /**
     * CustomerProfileEditor constructor.
     *
     * @param Validator $validator
     * @param LoggerInterface $logger
     */
    public function __construct(
        Validator $validator,
        LoggerInterface $logger
    ) {
        $this->logger = $logger;
        parent::__construct($validator);
    }

    /**
     * @param $value
     * @param string $name
     *
     * @return mixed
     * @throws AcceptableException
     */
    public function updated($value, string $name): mixed
    {
        $this->logger->info(sprintf(
            'Profile Update - Field: "%s", New Value: "%s"',
            $name,
            is_scalar($value) ? $value : json_encode($value)
        ));

        $this->validate();

        return $value;
    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->firstname = 'Guest';
        $this->lastname = 'User';
        $this->telephone = '+380';
    }

    /**
     * @return void
     * @throws AcceptableException
     */
    public function saveProfile(): void
    {
        $this->clearErrors();

        $this->validate();
        if ($this->hasErrors()) {
            return;
        }

        $this->dispatchBrowserEvent('profile-updated', [
            'message' => 'Profile has been successfully updated.'
        ]);
    }

    /**
     * @return void
     */
    public function resetProfile(): void
    {
        $this->firstname = '';
        $this->lastname = '';
        $this->telephone = '';
        $this->componentInitialized = false;
        $this->clearErrors();

        $this->dispatchBrowserEvent('profile-reset');
    }

    /**
     * @return void
     */
    public function booted()
    {
        $this->componentInitialized = true;
    }
}
