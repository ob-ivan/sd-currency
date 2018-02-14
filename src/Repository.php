<?php
namespace SD\Currency;

use SD\Currency\Model\Currency;
use SD\Currency\Model\Registry;
use SD\Currency\Model\Money;
use SD\Currency\Service\Converter;
use SD\Currency\Service\Formatter;
use SD\Currency\Service\FormatterInterface;
use SD\Currency\Service\Updater;
use SD\Currency\Store\Record;
use SD\Currency\Store\StoreInterface;

class Repository
{
    private $config;

    private $registry;

    /** @var StoreInterface */
    private $store = null;

    public function __construct(array $config = [])
    {
        $this->config = $config;
        $this->registry = new Registry();
    }

    // Model //

    public function getRegistry()
    {
        return $this->registry;
    }

    public function createMoney($amount, $currency)
    {
        if (is_string($currency)) {
            $currency = $this->registry->getByCode($currency);
        }
        return new Money(intval($amount), $currency);
    }

    // Service //

    public function getUpdater(array $config = [])
    {
        return new Updater($this->registry, $this->getStore(), $config);
    }

    /**
     * Create a formatter with provided format.
     *
     * Format may be specified as a format name from repository config
     * or as an array of config values.
     *
     *  @param string|array $formatNameOrConfig
     *  @return FormatterInterface
     *  @throws CurrencyException argument has invalid type
    **/
    public function getFormatter($formatNameOrConfig): FormatterInterface
    {
        $config = $this->config['formatter'] ?? [];
        $class = $config['class'] ?? Formatter::class;
        if (is_string($formatNameOrConfig)) {
            $formatConfig = $config[$formatNameOrConfig];
        } elseif (is_array($formatNameOrConfig)) {
            $formatConfig = $formatNameOrConfig;
        } else {
            throw new CurrencyException(
                'Argument 1 of ' . __METHOD__ . ' must be either string or array, ' .
                gettype($formatNameOrConfig) . ' given'
            );
        }
        return new $class($this->registry, $formatConfig);
    }

    public function getConverter()
    {
        return new Converter($this->registry, $this->getStore());
    }

    // Store //

    public function createRecord($code, $rate, $datetime)
    {
        return new Record($code, $rate, $datetime);
    }

    public function getOptions()
    {
        return array_map(
            function (Currency $currency) {
                $code = $currency->getCode();
                $record = $this->getStore()->get($code);
                return (object)[
                    'code' => $code,
                    'symbol' => $currency->getHtml(),
                    'rate' => floatval($record ? $record->getRate() : 0),
                ];
            },
            $this->registry->getAll()
        );
    }

    /**
     * @deprecated Pass store config to the constructor instead.
    **/
    public function setStore(StoreInterface $store)
    {
        trigger_error(__METHOD__ . ' is deprecated, use constructor config instead');
        $this->store = $store;
    }

    public function getStore(): ?StoreInterface
    {
        if (!$this->store) {
            if (isset($this->config['store'])) {
                $config = $this->config['store'];
                $class = $config['class'];
                $args = array_values($config['args'] ?? []);
                $this->store = new $class(...$args);
            }
        }
        return $this->store;
    }
}
